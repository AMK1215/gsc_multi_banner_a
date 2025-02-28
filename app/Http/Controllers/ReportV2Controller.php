<?php

namespace App\Http\Controllers;

use App\Models\Admin\BetresultBackup;
use App\Models\Admin\Product;
use App\Models\Admin\ResultBackup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportV2Controller extends Controller
{
    public function index(Request $request)
    {
        $adminId = auth()->id();

        $report = $this->buildQuery($request, $adminId);

        return view('report.v2.index', compact('report'));
    }

    public function getReportDetails(Request $request, $playerId)
    {

        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('is_active', 1)->get();

        return view('report.v2.detail', compact('details', 'productTypes', 'playerId'));
    }

    private function buildQuery(Request $request, $adminId)
    {
        $startDate = $request->start_date ??  Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $resultsSubquery = ResultBackup::select(
            'result_backups.user_id',
            DB::raw('SUM(result_backups.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(result_backups.win_amount) as win_amount'),
            DB::raw('SUM(result_backups.net_win) as net_win'),
            DB::raw('COUNT(result_backups.game_code) as total_count'),
        )
            ->groupBy('result_backups.user_id')
            ->whereBetween('result_backups.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $betsSubquery = BetresultBackup::select(
            'betresult_backups.user_id',
            DB::raw('SUM(betresult_backups.bet_amount) as bet_total_bet_amount'),
            DB::raw('SUM(betresult_backups.win_amount) as bet_total_win_amount'),
            DB::raw('SUM(betresult_backups.net_win) as bet_total_net_amount'),
            DB::raw('COUNT(betresult_backups.game_code) as total_count'),

        )
            ->groupBy('betresult_backups.user_id')
            ->whereBetween('betresult_backups.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $query = DB::table('users as players')
            ->select(
                'players.id as user_id',
                'players.name as player_name',
                'players.user_name as user_name',
                'agents.name as agent_name',
                DB::raw('IFNULL(result_backups.total_bet_amount, 0) + IFNULL(bets.bet_total_bet_amount, 0) as total_bet_amount'),
                DB::raw('IFNULL(result_backups.win_amount, 0) + IFNULL(bets.bet_total_win_amount, 0) as total_win_amount'),
                DB::raw('IFNULL(result_backups.net_win, 0) + IFNULL(bets.bet_total_net_amount, 0) as total_net_win'),
                DB::raw('IFNULL(result_backups.total_count, 0) + IFNULL(bets.total_count, 0) as total_count'),
                DB::raw('MAX(wallets.balance) as balance'),
            )
            ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
            ->leftJoin('wallets', 'wallets.holder_id', '=', 'players.id')
            ->leftJoinSub($resultsSubquery, 'result_backups', 'result_backups.user_id', '=', 'players.id') // Fixed alias
            ->leftJoinSub($betsSubquery, 'bets', 'bets.user_id', '=', 'players.id') // Fixed alias
            ->when($request->player_id, fn($query) => $query->where('players.user_name', $request->player_id))
            ->where(function ($query) {
                $query->whereNotNull('result_backups.user_id')
                    ->orWhereNotNull('bets.user_id');
            });

        $this->applyRoleFilter($query, $adminId);

        return $query->groupBy('players.id', 'players.name', 'players.user_name', 'agents.name')->get();
    }

    private function applyRoleFilter($query, $adminId)
    {
        if (Auth::user()->hasRole('Owner')) {
            $query->where('agents.agent_id', $adminId);
        } elseif (Auth::user()->hasRole('Agent')) {
            $query->where('agents.id', $adminId);
        }
    }

    private function getPlayerDetails($playerId, $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfMonth()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfMonth()->format('Y-m-d H:i');


        $combinedSubquery = DB::table('result_backups')
            ->select(
                'user_id',
                'total_bet_amount',
                'win_amount',
                'net_win',
                'game_lists.game_name',
                'products.provider_name',
                'result_backups.created_at as date',
                'round_id'
            )
            ->join('game_lists', 'game_lists.game_id', '=', 'result_backups.game_code')
            ->join('products', 'products.id', '=', 'game_lists.product_id')
            ->whereBetween('result_backups.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($request->product_id, fn($query) => $query->where('products.id', $request->product_id))
            ->unionAll(
                DB::table('betresult_backups')
                    ->select(
                        'user_id',
                        'bet_amount as total_bet_amount',
                        'win_amount',
                        'net_win',
                        'game_lists.game_name',
                        'products.provider_name',
                        'betresult_backups.created_at as date',
                        'tran_id as round_id'
                    )
                    ->join('game_lists', 'game_lists.game_id', '=', 'betresult_backups.game_code')
                    ->join('products', 'products.id', '=', 'game_lists.product_id')
                    ->whereBetween('betresult_backups.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when($request->product_id, fn($query) => $query->where('products.id', $request->product_id))
            );

        $query = DB::table('users as players')
            ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
            ->where('players.id', $playerId);

        return $query->orderBy('date', 'desc')->get();
    }
}
