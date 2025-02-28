<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use App\Models\Webhook\BetNResult;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $adminId = auth()->id();

        $report = $this->buildQuery($request, $adminId);

        return view('admin.reports.index', compact('report'));
    }
    
    public function getReportDetails(Request $request, $playerId)
    {

        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('is_active', 1)->get();

        return view('admin.reports.detail', compact('details','productTypes', 'playerId'));
    }


    public function getTransactionDetails($tranId)
    {
        $operatorId = 'delightMMK';

        $url = 'https://api.sm-sspi-prod.com/api/opgateway/v1/op/GetTransactionDetails';

        // Generate the RequestDateTime in UTC
        $requestDateTime = Carbon::now('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $secretKey = '1OMJXOf88RHKpcuT';
        $functionName = 'GetTransactionDetails';
        $signatureString = $functionName.$requestDateTime.$operatorId.$secretKey;
        $signature = md5($signatureString);

        // Prepare request payload
        $payload = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'TranId' => $tranId,
        ];

        try {
            $response = Http::post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to get transaction details', ['response' => $response->body()]);

                return response()->json(['error' => 'Failed to get transaction details'], 500);
            }
        } catch (\Exception $e) {
            Log::error('API request error', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'API request error'], 500);
        }
    }

    public function BoReport()
    {
        $response = Http::post('https://agdashboard.pro/proxy-to-bo/', [
            'username' => 'delightMMK',
            'password' => '123456',
        ]);

        if ($response->ok()) {
            return $response->body(); // Display the response from the backend
        } else {
            return 'Error: Unable to login';
        }
    }

    private function buildQuery(Request $request, $adminId)
    {
        $startDate = $request->start_date ??  Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString() ;

        $resultsSubquery = Result::select(
            'results.user_id',
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.win_amount) as win_amount'),
            DB::raw('SUM(results.net_win) as net_win'),
            DB::raw('COUNT(results.game_code) as total_count'),
        )
            ->groupBy('results.user_id')
            ->whereBetween('results.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $betsSubquery = BetNResult::select(
            'bet_n_results.user_id',
            DB::raw('SUM(bet_n_results.bet_amount) as bet_total_bet_amount'),
            DB::raw('SUM(bet_n_results.win_amount) as bet_total_win_amount'),
            DB::raw('SUM(bet_n_results.net_win) as bet_total_net_amount'),
            DB::raw('COUNT(bet_n_results.game_code) as total_count'),

        )
            ->groupBy('bet_n_results.user_id')
            ->whereBetween('bet_n_results.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $query = DB::table('users as players')
            ->select(
                'players.id as user_id',
                'players.name as player_name',
                'players.user_name as user_name',
                'agents.name as agent_name',
                DB::raw('IFNULL(results.total_bet_amount, 0) + IFNULL(bets.bet_total_bet_amount, 0) as total_bet_amount'),
                DB::raw('IFNULL(results.win_amount, 0) + IFNULL(bets.bet_total_win_amount, 0) as total_win_amount'),
                DB::raw('IFNULL(results.net_win, 0) + IFNULL(bets.bet_total_net_amount, 0) as total_net_win'),
                DB::raw('IFNULL(results.total_count, 0) + IFNULL(bets.total_count, 0) as total_count'),
                DB::raw('MAX(wallets.balance) as balance'),
            )
            ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
            ->leftJoin('wallets', 'wallets.holder_id', '=', 'players.id')
            ->leftJoinSub($resultsSubquery, 'results', 'results.user_id', '=', 'players.id') // Fixed alias
            ->leftJoinSub($betsSubquery, 'bets', 'bets.user_id', '=', 'players.id') // Fixed alias
            ->when($request->player_id, fn ($query) => $query->where('players.user_name', $request->player_id))
            ->where(function ($query) {
                $query->whereNotNull('results.user_id')
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
        $startDate = $request->start_date ??  Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString() ;

        $combinedSubquery = DB::table('results')
            ->select(
                'user_id',
                'total_bet_amount',
                'win_amount',
                'net_win',
                'game_lists.game_name',
                'products.provider_name',
                'results.created_at as date',
                'round_id'
            )
            ->join('game_lists', 'game_lists.game_id', '=', 'results.game_code')
            ->join('products', 'products.id', '=', 'game_lists.product_id')
            ->whereBetween('results.created_at', [$startDate . ' 00:00:00', $endDate .' 23:59:59'])
            ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            ->unionAll(
                DB::table('bet_n_results')
                    ->select(
                        'user_id',
                        'bet_amount as total_bet_amount',
                        'win_amount',
                        'net_win',
                        'game_lists.game_name',
                        'products.provider_name',
                        'bet_n_results.created_at as date',
                        'tran_id as round_id'
                    )
                    ->join('game_lists', 'game_lists.game_id', '=', 'bet_n_results.game_code')
                    ->join('products', 'products.id', '=', 'game_lists.product_id')
                    ->whereBetween('bet_n_results.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            );

        $query = DB::table('users as players')
            ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
            ->where('players.id', $playerId);
      
        return $query->orderBy('date', 'desc')->get();
    }
}
