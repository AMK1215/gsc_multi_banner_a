<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
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
        $admin = Auth::user();

        $hierarchy = [
            'Owner' => ['Agent'],
        ];
        $reportQuery = $this->buildQuery($request);

        if ($admin->hasRole('Senior')) {
            $reports = $reportQuery->get();
        } elseif ($admin->hasRole('Agent')) {
            $agentChildrenIds = $admin->children->pluck('id')->toArray();
            $reports = $reportQuery->whereIn('players.id', $agentChildrenIds)->get();
        } else {
            $agentChildrenIds = $this->getAgentChildrenIds($admin, $hierarchy);
            $reports = $reportQuery->whereIn('players.id', $agentChildrenIds)->get();
        }

        return view('admin.reports.index', compact('reports'));
    }

    public function getReportDetails(Request $request, $playerId)
    {

        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('status', 1)->get();

        return view('admin.reports.detail', compact('details', 'productTypes', 'playerId'));
    }

    private function buildQuery(Request $request)
    {
        $startDate = $request->start_date ??  Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfMonth()->toDateString();

        $query = DB::table('users as players')
            ->leftJoin('reports', 'players.user_name', '=', 'reports.member_name')
            ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
            ->select(
                'players.id as player_id',
                'players.user_name',
                'players.name',
                'agents.name as agent_name',
                DB::raw('SUM(reports.payout_amount) as total_payout'),
                DB::raw('SUM(reports.bet_amount) as total_bet'),
                DB::raw('count(reports.product_code) as total_count')
            )
            ->whereBetween('reports.created_at', [$startDate, $endDate])
            ->where('reports.status', '101');

        return $query->groupBy('players.id', 'players.user_name', 'players.name', 'agents.name');
    }

    private function getPlayerDetails($playerId, $request)
    {
        $startDate = $request->start_date ??  Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfMonth()->toDateString();
        $query = DB::table('users')
            ->leftJoin('reports', 'reports.member_name', '=', 'users.user_name')
            ->leftJoin('products', 'products.code', '=', 'reports.product_code')
            ->leftJoin(DB::raw('(SELECT code, MAX(name) as name FROM game_lists GROUP BY code) as game_lists'), 'game_lists.code', '=', 'reports.game_name')
            ->where('users.id', $playerId)
            ->where('reports.status', 101)
            ->whereBetween('reports.created_at', [$startDate, $endDate])
            ->when($request->product_code, fn($query) => $query->where('reports.product_code', $request->product_code))
            ->select(
                'reports.id',
                'reports.member_name',
                'products.name as product_name',
                'reports.bet_amount',
                'reports.payout_amount',
                'reports.game_round_id',
                'reports.created_at',
                'game_lists.name as game_name'
            );

        return $query->orderBy('reports.id', 'desc')->get();
    }

    private function getAgentChildrenIds($agent, array $hierarchy)
    {
        foreach ($hierarchy as $role => $levels) {
            if ($agent->hasRole($role)) {
                return collect([$agent])
                    ->flatMap(fn($levelAgent) => $this->getChildrenRecursive($levelAgent, $levels))
                    ->pluck('id')
                    ->toArray();
            }
        }
        return [];
    }

    private function getChildrenRecursive($agent, array $levels)
    {
        $children = collect([$agent]);
        foreach ($levels as $level) {
            $children = $children->flatMap->children;
        }
        return $children->flatMap->children;
    }
}
