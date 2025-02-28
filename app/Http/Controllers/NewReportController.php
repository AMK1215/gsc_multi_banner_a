<?php

namespace App\Http\Controllers;

use App\Models\Admin\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Webhook\Result;
use App\Models\Webhook\Bet;
use App\Models\Webhook\BetNResult;



class NewReportController extends Controller
{

    public function getAllResults()
    {
        // Fetch results with pagination (10 results per page)
        $results = Result::orderBy('created_at', 'asc')->paginate(10);

        // Pass the results to the view
        return view('admin.reports.senior.result_index', compact('results'));
    }

    public function deleteResults(Request $request)
    {
        // Validate the request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Delete results within the specified date range
        $deletedRows = Result::whereBetween('created_at', [$startDate, $endDate])->delete();

        if ($deletedRows) {
            return redirect()->route('admin.senior_results.index')->with('success', 'Results deleted successfully.');
        } else {
            return redirect()->route('admin.senior_results.index')->with('error', 'No results found to delete.');
        }
    }


    public function getAllBets()
    {
        // Fetch results with pagination (10 results per page)
        $results = Bet::orderBy('created_at', 'asc')->paginate(10);

        // Pass the results to the view
        return view('admin.reports.senior.bet_index', compact('results'));
    }

    public function deleteBets(Request $request)
    {
        // Validate the request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Delete results within the specified date range
        $deletedRows = Bet::whereBetween('created_at', [$startDate, $endDate])->delete();

        if ($deletedRows) {
            return redirect()->route('admin.senior_bet.index')->with('success', 'Bet deleted successfully.');
        } else {
            return redirect()->route('admin.senior_bet.index')->with('error', 'No Bet found to delete.');
        }
    }


    public function getAllJili()
    {
        // Fetch results with pagination (10 results per page)
        $results = BetNResult::orderBy('created_at', 'asc')->paginate(10);

        // Pass the results to the view
        return view('admin.reports.senior.bet_n_result_index', compact('results'));
    }

    public function deleteJili(Request $request)
    {
        // Validate the request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Delete results within the specified date range
        $deletedRows = BetNResult::whereBetween('created_at', [$startDate, $endDate])->delete();

        if ($deletedRows) {
            return redirect()->route('admin.senior_bet_n_result.index')->with('success', 'BetNResult deleted successfully.');
        } else {
            return redirect()->route('admin.senior_bet_n_result.index')->with('error', 'No BetNResult found to delete.');
        }
    }





    public function getGameReport(Request $request)
{
    // Generate a unique cache key
    $cacheKey = 'game_report_' . md5(json_encode($request->all()));

    // Check if the report exists in cache, otherwise fetch and store it
    if (!Cache::has($cacheKey)) {
        $report = $this->fetchGameReport($request);
        Cache::put($cacheKey, serialize($report), now()->addMinutes(5)); // Store serialized data
    } else {
        $report = unserialize(Cache::get($cacheKey)); // Retrieve and unserialize
    }

    return view('report.index', compact('report'));
}

/**
 * Fetch the game report data.
 */
private function fetchGameReport(Request $request)
{
    return DB::query()
        ->fromSub(function ($query) use ($request) {
            $betData = DB::table('bet_n_results as br')
                ->select(
                    'br.player_id',
                    DB::raw('NULL as player_name'),
                    'br.game_code',
                    'br.game_name',
                    'br.provider_code as game_provide_name',
                    DB::raw('COUNT(br.id) as total_bets'),
                    DB::raw('ROUND(SUM(br.bet_amount), 2) as total_bet_amount'),
                    DB::raw('ROUND(SUM(br.win_amount), 2) as total_win_amount'),
                    DB::raw('ROUND(SUM(br.net_win), 2) as total_net_win'),
                    DB::raw('0 as total_results'),
                    DB::raw('NULL as total_result_bet_amount'),
                    DB::raw('NULL as total_result_win_amount'),
                    DB::raw('NULL as total_result_net_win')
                )
                ->when($request->filled('start_date'), fn($q) => $q->where('br.created_at', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->where('br.created_at', '<=', $request->end_date))
                ->when($request->filled('user_id'), fn($q) => $q->where('br.player_id', $request->user_id))
                ->groupBy('br.player_id', 'br.game_code', 'br.game_name', 'br.provider_code');

            $resultData = DB::table('results as r')
                ->select(
                    'r.player_id',
                    'r.player_name',
                    'r.game_code',
                    'r.game_name',
                    'r.game_provide_name',
                    DB::raw('0 as total_bets'),
                    DB::raw('NULL as total_bet_amount'),
                    DB::raw('NULL as total_win_amount'),
                    DB::raw('NULL as total_net_win'),
                    DB::raw('COUNT(r.id) as total_results'),
                    DB::raw('ROUND(SUM(r.total_bet_amount), 2) as total_result_bet_amount'),
                    DB::raw('ROUND(SUM(r.win_amount), 2) as total_result_win_amount'),
                    DB::raw('ROUND(SUM(r.net_win), 2) as total_result_net_win')
                )
                ->when($request->filled('start_date'), fn($q) => $q->where('r.created_at', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->where('r.created_at', '<=', $request->end_date))
                ->when($request->filled('user_id'), fn($q) => $q->where('r.player_id', $request->user_id))
                ->groupBy('r.player_id', 'r.game_code', 'r.game_name', 'r.game_provide_name', 'r.player_name');

            $query->from($betData)->unionAll($resultData);
        }, 'combined_data')
        ->select(
            'player_id',
            DB::raw('COALESCE(player_name, player_id) as player_name'),
            'game_code',
            DB::raw('COALESCE(game_name, game_code) as game_name'),
            'game_provide_name',
            DB::raw('SUM(total_bets) as total_bets'),
            DB::raw('ROUND(SUM(total_bet_amount), 2) as total_bet_amount'),
            DB::raw('ROUND(SUM(total_win_amount), 2) as total_win_amount'),
            DB::raw('ROUND(SUM(total_net_win), 2) as total_net_win'),
            DB::raw('SUM(total_results) as total_results'),
            DB::raw('ROUND(SUM(total_result_bet_amount), 2) as total_result_bet_amount'),
            DB::raw('ROUND(SUM(total_result_win_amount), 2) as total_result_win_amount'),
            DB::raw('ROUND(SUM(total_result_net_win), 2) as total_result_net_win')
        )
        ->groupBy('player_id', 'game_code', 'game_name', 'game_provide_name', 'player_name')
        ->orderByDesc('total_bets')
        ->paginate(10);
}


    // public function getGameReport(Request $request)
    // {
    //     // Fetch report data with pagination
    //     $report = DB::query()
    //         ->fromSub(function ($query) {
    //             $betData = DB::table('bet_n_results as br')
    //                 ->select(
    //                     'br.player_id',
    //                     DB::raw('NULL as player_name'),
    //                     'br.game_code',
    //                     'br.game_name',
    //                     'br.provider_code as game_provide_name',
    //                     DB::raw('COUNT(br.id) as total_bets'),
    //                     DB::raw('ROUND(SUM(br.bet_amount), 2) as total_bet_amount'),
    //                     DB::raw('ROUND(SUM(br.win_amount), 2) as total_win_amount'),
    //                     DB::raw('ROUND(SUM(br.net_win), 2) as total_net_win'),
    //                     DB::raw('0 as total_results'),
    //                     DB::raw('NULL as total_result_bet_amount'),
    //                     DB::raw('NULL as total_result_win_amount'),
    //                     DB::raw('NULL as total_result_net_win')
    //                 )
    //                 ->groupBy('br.player_id', 'br.game_code', 'br.game_name', 'br.provider_code');

    //             $resultData = DB::table('results as r')
    //                 ->select(
    //                     'r.player_id',
    //                     'r.player_name',
    //                     'r.game_code',
    //                     'r.game_name',
    //                     'r.game_provide_name',
    //                     DB::raw('0 as total_bets'),
    //                     DB::raw('NULL as total_bet_amount'),
    //                     DB::raw('NULL as total_win_amount'),
    //                     DB::raw('NULL as total_net_win'),
    //                     DB::raw('COUNT(r.id) as total_results'),
    //                     DB::raw('ROUND(SUM(r.total_bet_amount), 2) as total_result_bet_amount'),
    //                     DB::raw('ROUND(SUM(r.win_amount), 2) as total_result_win_amount'),
    //                     DB::raw('ROUND(SUM(r.net_win), 2) as total_result_net_win')
    //                 )
    //                 ->groupBy('r.player_id', 'r.game_code', 'r.game_name', 'r.game_provide_name', 'r.player_name');

    //             $query->from($betData)
    //                 ->unionAll($resultData);
    //         }, 'combined_data')
    //         ->select(
    //             'player_id',
    //             DB::raw('COALESCE(player_name, player_id) as player_name'),
    //             'game_code',
    //             DB::raw('COALESCE(game_name, game_code) as game_name'),
    //             'game_provide_name',
    //             DB::raw('SUM(total_bets) as total_bets'),
    //             DB::raw('ROUND(SUM(total_bet_amount), 2) as total_bet_amount'),
    //             DB::raw('ROUND(SUM(total_win_amount), 2) as total_win_amount'),
    //             DB::raw('ROUND(SUM(total_net_win), 2) as total_net_win'),
    //             DB::raw('SUM(total_results) as total_results'),
    //             DB::raw('ROUND(SUM(total_result_bet_amount), 2) as total_result_bet_amount'),
    //             DB::raw('ROUND(SUM(total_result_win_amount), 2) as total_result_win_amount'),
    //             DB::raw('ROUND(SUM(total_result_net_win), 2) as total_result_net_win')
    //         )
    //         ->groupBy('player_id', 'game_code', 'game_name', 'game_provide_name', 'player_name')
    //         ->orderByDesc('total_bets')
    //         ->paginate(10); // Pagination enabled

    //     return view('report.index', compact('report'));
    // }
    // public function getGameReport(Request $request)
    // {
    //     // Fetch report data with pagination
    //     $query = DB::query()
    //         ->fromSub(function ($query) {
    //             $betData = DB::table('bet_n_results as br')
    //                 ->select(
    //                     'br.player_id',
    //                     DB::raw('NULL as player_name'),
    //                     'br.game_code',
    //                     'br.game_name',
    //                     'br.provider_code as game_provide_name',
    //                     DB::raw('COUNT(br.id) as total_bets'),
    //                     DB::raw('ROUND(SUM(br.bet_amount), 2) as total_bet_amount'),
    //                     DB::raw('ROUND(SUM(br.win_amount), 2) as total_win_amount'),
    //                     DB::raw('ROUND(SUM(br.net_win), 2) as total_net_win'),
    //                     DB::raw('0 as total_results'),
    //                     DB::raw('NULL as total_result_bet_amount'),
    //                     DB::raw('NULL as total_result_win_amount'),
    //                     DB::raw('NULL as total_result_net_win'),
    //                     'br.created_at'
    //                 )
    //                 ->groupBy('br.player_id', 'br.game_code', 'br.game_name', 'br.provider_code', 'br.created_at');

    //             $resultData = DB::table('results as r')
    //                 ->select(
    //                     'r.player_id',
    //                     'r.player_name',
    //                     'r.game_code',
    //                     'r.game_name',
    //                     'r.game_provide_name',
    //                     DB::raw('0 as total_bets'),
    //                     DB::raw('NULL as total_bet_amount'),
    //                     DB::raw('NULL as total_win_amount'),
    //                     DB::raw('NULL as total_net_win'),
    //                     DB::raw('COUNT(r.id) as total_results'),
    //                     DB::raw('ROUND(SUM(r.total_bet_amount), 2) as total_result_bet_amount'),
    //                     DB::raw('ROUND(SUM(r.win_amount), 2) as total_result_win_amount'),
    //                     DB::raw('ROUND(SUM(r.net_win), 2) as total_result_net_win'),
    //                     'r.created_at'
    //                 )
    //                 ->groupBy('r.player_id', 'r.game_code', 'r.game_name', 'r.game_provide_name', 'r.player_name', 'r.created_at');

    //             $query->from($betData)
    //                 ->unionAll($resultData);
    //         }, 'combined_data')
    //         ->select(
    //             'player_id',
    //             DB::raw('COALESCE(player_name, player_id) as player_name'),
    //             'game_code',
    //             DB::raw('COALESCE(game_name, game_code) as game_name'),
    //             'game_provide_name',
    //             DB::raw('SUM(total_bets) as total_bets'),
    //             DB::raw('ROUND(SUM(total_bet_amount), 2) as total_bet_amount'),
    //             DB::raw('ROUND(SUM(total_win_amount), 2) as total_win_amount'),
    //             DB::raw('ROUND(SUM(total_net_win), 2) as total_net_win'),
    //             DB::raw('SUM(total_results) as total_results'),
    //             DB::raw('ROUND(SUM(total_result_bet_amount), 2) as total_result_bet_amount'),
    //             DB::raw('ROUND(SUM(total_result_win_amount), 2) as total_result_win_amount'),
    //             DB::raw('ROUND(SUM(total_result_net_win), 2) as total_result_net_win'),
    //             'created_at'
    //         )
    //         ->groupBy('player_id', 'game_code', 'game_name', 'game_provide_name', 'player_name', 'created_at');

    //     // Apply filters BEFORE paginating
    //     if ($request->has('start_date')) {
    //         $query->whereDate('created_at', '>=', $request->start_date);
    //     }
    //     if ($request->has('end_date')) {
    //         $query->whereDate('created_at', '<=', $request->end_date);
    //     }
    //     if ($request->has('user_id')) {
    //         $query->where('player_id', $request->user_id);
    //     }

    //     // Paginate after filtering
    //     $report = $query->orderByDesc('total_bets')->paginate(10);

    //     return view('report.v2_report_index', compact('report'));
    // }

    // public function getGameReport(Request $request)
    // {
    //     $query = DB::table('bet_n_results as br')
    //         ->leftJoin('results as r', function ($join) {
    //             $join->on('br.player_id', '=', 'r.player_id')
    //                 ->on('br.game_code', '=', 'r.game_code');
    //         })
    //         ->select(
    //             'br.player_id',
    //             DB::raw('COALESCE(r.player_name, br.player_id) as player_name'),
    //             'br.game_code',
    //             'br.game_name',
    //             'br.provider_code as game_provide_name',
    //             DB::raw('SUM(br.bet_amount) as total_bet_amount'),
    //             DB::raw('SUM(br.win_amount) as total_win_amount'),
    //             DB::raw('SUM(br.net_win) as total_net_win'),
    //             DB::raw('SUM(br.commission_amount) as commission'),
    //             DB::raw('SUM(r.win_amount) as pt_win_loss'),
    //             DB::raw('SUM(r.commission_amount) as pt_commission'),
    //             DB::raw('COALESCE(SUM(br.old_balance), 0) as old_balance'),
    //             DB::raw('COALESCE(SUM(br.new_balance), 0) as new_balance')
    //         )
    //         ->groupBy('br.player_id', 'br.game_code', 'br.game_name', 'br.provider_code', 'r.player_name');

    //     // Apply filters
    //     if ($request->has('start_date')) {
    //         $query->whereDate('br.created_at', '>=', $request->start_date);
    //     }
    //     if ($request->has('end_date')) {
    //         $query->whereDate('br.created_at', '<=', $request->end_date);
    //     }
    //     if ($request->has('user_id')) {
    //         $query->where('br.player_id', $request->user_id);
    //     }

    //     $report = $query->paginate(10);

    //     return view('report.index', compact('report'));
    // }

    public function getGameReportDetail($player_id, $game_code)
    {
        // Fetch bet details from bet_n_results
        $betData = DB::table('bet_n_results as br')
            ->select(
                'br.player_id',
                DB::raw('NULL as player_name'),
                'br.game_code',
                'br.game_name',
                'br.provider_code as game_provide_name',
                'br.bet_amount',
                'br.win_amount',
                'br.net_win',
                DB::raw('IFNULL(br.old_balance, 0) as old_balance'), // Fix NULL issue
                DB::raw('IFNULL(br.new_balance, 0) as new_balance'), // Fix NULL issue
                DB::raw('NULL as total_bet_amount'),
                DB::raw('NULL as result_win_amount'),
                DB::raw('NULL as result_net_win'),
                'br.created_at as bet_time',
                DB::raw('NULL as result_time')
            )
            ->where('br.player_id', $player_id)
            ->where('br.game_code', $game_code);

        // Fetch result details from results
        $resultData = DB::table('results as r')
            ->select(
                'r.player_id',
                'r.player_name',
                'r.game_code',
                'r.game_name',
                'r.game_provide_name',
                DB::raw('NULL as bet_amount'),
                DB::raw('NULL as win_amount'),
                DB::raw('NULL as net_win'),
                DB::raw('IFNULL(r.old_balance, 0) as old_balance'), // Fix NULL issue
                DB::raw('IFNULL(r.new_balance, 0) as new_balance'), // Fix NULL issue
                'r.total_bet_amount',
                'r.win_amount as result_win_amount',
                'r.net_win as result_net_win',
                DB::raw('NULL as bet_time'),
                'r.created_at as result_time'
            )
            ->where('r.player_id', $player_id)
            ->where('r.game_code', $game_code);

        // Combine both datasets using UNION
        $details = DB::query()
            ->fromSub(function ($query) use ($betData, $resultData) {
                $query->from($betData)
                    ->unionAll($resultData);
            }, 'combined_data')
            ->select(
                'player_id',
                DB::raw('COALESCE(player_name, player_id) as player_name'),
                'game_code',
                DB::raw('COALESCE(game_name, game_code) as game_name'),
                'game_provide_name',
                DB::raw('COALESCE(bet_amount, total_bet_amount) as total_bet_amount'),
                DB::raw('COALESCE(win_amount, result_win_amount) as total_win_amount'),
                DB::raw('COALESCE(net_win, result_net_win) as total_net_win'),
                DB::raw('COALESCE(old_balance, 0) as old_balance'), // Fix NULL issue
                DB::raw('COALESCE(new_balance, 0) as new_balance'), // Fix NULL issue
                'bet_time',
                'result_time'
            )
            ->orderByDesc(DB::raw('COALESCE(bet_time, result_time)'))
            ->get();

        // Fetch active product types
        $productTypes = Product::where('is_active', 1)->get();

        return view('report.detail', compact('details', 'productTypes'));
    }

    // for agent
    public function getGameAgentReport(Request $request)
{
    // Get the authenticated agent's related players
    $authUser = Auth::user();
    $relatedPlayers = $authUser->children()->pluck('id'); // Fetch related player IDs under agent

    // Generate a unique cache key
    $cacheKey = 'game_report_' . md5(json_encode($request->all()) . $authUser->id);

    // Check if the report exists in cache, otherwise fetch and store it
    if (!Cache::has($cacheKey)) {
        $report = $this->fetchGameAgentReport($request, $relatedPlayers);
        Cache::put($cacheKey, serialize($report), now()->addMinutes(5)); // Store serialized data
    } else {
        $report = unserialize(Cache::get($cacheKey)); // Retrieve and unserialize
    }

    return view('report.agent_index', compact('report'));
}

/**
 * Fetch the game report data for agent's players only.
 */
private function fetchGameAgentReport(Request $request, $relatedPlayers)
{
    return DB::query()
        ->fromSub(function ($query) use ($request, $relatedPlayers) {
            $betData = DB::table('bet_n_results as br')
                ->select(
                    'br.player_id',
                    DB::raw('NULL as player_name'),
                    'br.game_code',
                    'br.game_name',
                    'br.provider_code as game_provide_name',
                    DB::raw('COUNT(br.id) as total_bets'),
                    DB::raw('ROUND(SUM(br.bet_amount), 2) as total_bet_amount'),
                    DB::raw('ROUND(SUM(br.win_amount), 2) as total_win_amount'),
                    DB::raw('ROUND(SUM(br.net_win), 2) as total_net_win'),
                    DB::raw('0 as total_results'),
                    DB::raw('NULL as total_result_bet_amount'),
                    DB::raw('NULL as total_result_win_amount'),
                    DB::raw('NULL as total_result_net_win')
                )
                ->whereIn('br.player_id', $relatedPlayers) // Fetch only agent-related players
                ->when($request->filled('start_date'), fn($q) => $q->where('br.created_at', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->where('br.created_at', '<=', $request->end_date))
                ->groupBy('br.player_id', 'br.game_code', 'br.game_name', 'br.provider_code');

            $resultData = DB::table('results as r')
                ->select(
                    'r.player_id',
                    'r.player_name',
                    'r.game_code',
                    'r.game_name',
                    'r.game_provide_name',
                    DB::raw('0 as total_bets'),
                    DB::raw('NULL as total_bet_amount'),
                    DB::raw('NULL as total_win_amount'),
                    DB::raw('NULL as total_net_win'),
                    DB::raw('COUNT(r.id) as total_results'),
                    DB::raw('ROUND(SUM(r.total_bet_amount), 2) as total_result_bet_amount'),
                    DB::raw('ROUND(SUM(r.win_amount), 2) as total_result_win_amount'),
                    DB::raw('ROUND(SUM(r.net_win), 2) as total_result_net_win')
                )
                ->whereIn('r.player_id', $relatedPlayers) // Fetch only agent-related players
                ->when($request->filled('start_date'), fn($q) => $q->where('r.created_at', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->where('r.created_at', '<=', $request->end_date))
                ->groupBy('r.player_id', 'r.game_code', 'r.game_name', 'r.game_provide_name', 'r.player_name');

            $query->from($betData)->unionAll($resultData);
        }, 'combined_data')
        ->select(
            'player_id',
            DB::raw('COALESCE(player_name, player_id) as player_name'),
            'game_code',
            DB::raw('COALESCE(game_name, game_code) as game_name'),
            'game_provide_name',
            DB::raw('SUM(total_bets) as total_bets'),
            DB::raw('ROUND(SUM(total_bet_amount), 2) as total_bet_amount'),
            DB::raw('ROUND(SUM(total_win_amount), 2) as total_win_amount'),
            DB::raw('ROUND(SUM(total_net_win), 2) as total_net_win'),
            DB::raw('SUM(total_results) as total_results'),
            DB::raw('ROUND(SUM(total_result_bet_amount), 2) as total_result_bet_amount'),
            DB::raw('ROUND(SUM(total_result_win_amount), 2) as total_result_win_amount'),
            DB::raw('ROUND(SUM(total_result_net_win), 2) as total_result_net_win')
        )
        ->groupBy('player_id', 'game_code', 'game_name', 'game_provide_name', 'player_name')
        ->orderByDesc('total_bets')
        ->paginate(10);
}

}