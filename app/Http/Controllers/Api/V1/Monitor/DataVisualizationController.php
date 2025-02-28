<?php

namespace App\Http\Controllers\Api\V1\Monitor;

use App\Http\Controllers\Controller;
use App\Models\Webhook\Bet;
use App\Models\Webhook\Result;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import Log facade
use Illuminate\Support\Facades\Log;

class DataVisualizationController extends Controller
{
    use HttpResponses;

    public function VisualizeBet()
    {
        Log::info('VisualizeBet API called.');

        try {
            Log::info('Fetching Bets...');
            $bets = DB::table('bets')->get();
            Log::info('Bets retrieved successfully.', ['count' => $bets->count()]);

            return $this->success($bets, 'Bets retrieved successfully.');
        } catch (\Exception $e) {
            Log::error('Error in VisualizeBet:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error(null, 'Failed to retrieve bets.', 500);
        }
    }

    public function VisualizeResult()
    {
        Log::info('VisualizeResult API called.');

        try {
            Log::info('Fetching Results...');
            $results = DB::table('results')->get();
            Log::info('Results retrieved successfully.', ['count' => $results->count()]);

            return $this->success($results, 'Results retrieved successfully.');
        } catch (\Exception $e) {
            Log::error('Error in VisualizeResult:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error(null, 'Failed to retrieve results.', 500);
        }
    }

    public function getResultsData(Request $request)
    {
        try {
            $adminId = auth()->id(); // Optional: for filtering based on admin

            $report = Result::select(
                DB::raw('SUM(total_bet_amount) as total_bet_amount'),
                DB::raw('SUM(win_amount) as total_win_amount'),
                DB::raw('SUM(net_win) as total_net_win'),
                DB::raw('COUNT(*) as total_games'),
                'players.name as player_name',
                'agents.name as agent_name',
                'players.id as user_id'
            )
                ->join('users as players', 'results.user_id', '=', 'players.id')
                ->join('users as agents', 'players.agent_id', '=', 'agents.id')
                ->groupBy('players.name', 'agents.name', 'players.id')->get();

            return $this->success($report, 'Results data retrieved successfully.');
        } catch (\Exception $e) {
            return $this->error(null, 'Failed to retrieve results data.', 500);
        }
    }

    //    public function VisualizeBet()
    // {
    //     Log::info('VisualizeBet API called.');

    //     try {
    //         Log::info('Fetching Bets...');

    //         // Check if database connection is working
    //         $connection = DB::connection()->getPdo();
    //         Log::info('Database connection successful.');

    //         // Fetch the data
    //         $bets = Bet::all();
    //         Log::info('Bets retrieved successfully.', ['count' => $bets->count()]);

    //         return $this->success($bets, 'Bets retrieved successfully.');
    //     } catch (\PDOException $e) {
    //         Log::error('Database error:', [
    //             'error' => $e->getMessage(),
    //         ]);
    //         return $this->error(null, 'Database connection failed.', 500);
    //     } catch (\Exception $e) {
    //         Log::error('Error in VisualizeBet:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return $this->error(null, 'Failed to retrieve bets.', 500);
    //     }
    // }

    // public function VisualizeResult()
    // {
    //     Log::info('VisualizeResult API called.');

    //     try {
    //         Log::info('Fetching Results...');

    //         // Check if database connection is working
    //         $connection = DB::connection()->getPdo();
    //         Log::info('Database connection successful.');

    //         // Fetch the data
    //         $results = Result::all();
    //         Log::info('Results retrieved successfully.', ['count' => $results->count()]);

    //         return $this->success($results, 'Results retrieved successfully.');
    //     } catch (\PDOException $e) {
    //         Log::error('Database error:', [
    //             'error' => $e->getMessage(),
    //         ]);
    //         return $this->error(null, 'Database connection failed.', 500);
    //     } catch (\Exception $e) {
    //         Log::error('Error in VisualizeResult:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return $this->error(null, 'Failed to retrieve results.', 500);
    //     }
    // }

}
