<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Admin\ReportTransaction;
use App\Services\WalletService;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $type = $request->get('type');

        [$from, $to] = match ($type) {
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            default => [now()->startOfDay(), now()],
        };

        $transactions = Auth::user()->transactions()
            ->whereIn('transactions.type', ['withdraw', 'deposit'])
            ->whereIn('transactions.name', ['credit_transfer', 'debit_transfer'])
            ->latest()->get();

        return $this->success(TransactionResource::collection($transactions));
    }

    public function getTransactionDetails(Request $request)
    {
        $validatedData = $request->validate([
            'agentCode' => 'required',
            'WagerID' => 'required',
        ]);

        $user = Auth::user();
        $operatorCode = Config::get('game.api.operator_code');
        $secretKey = Config::get('game.api.secret_key');
        $apiUrl = Config::get('game.api.url').'/Seamless/LaunchGame';
        $password = Config::get('game.api.password');
        // Generate the signature
        $requestTime = now()->format('YmdHis');
        $signature = md5($operatorCode.$requestTime.'launchgame'.$secretKey);

        // Prepare the payload
        $data = [
            'agentCode' => $operatorCode,
            'WagerID' => $request->wager_id, // Assume username is the member identifier
         
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json(['error' => 'API request failed', 'details' => $response->body()], $response->status());
        } catch (\Throwable $e) {

            return response()->json(['error' => 'An unexpected error occurred', 'exception' => $e->getMessage()], 500);
        }
    }

    public function GetPlayerShanReport()
    {
        // Get the authenticated player's ID
        $user_id = Auth::id();

        // Query to get all report transactions for the authenticated user
        $userTransactions = ReportTransaction::where('user_id', $user_id)
            ->orderByDesc('created_at')
            ->get();

        // Get player name
        $player = Auth::user();
        $playerName = $player ? $player->user_name : 'Unknown';

        // Calculate Total Bet Amount
        $totalBet = $userTransactions->sum('bet_amount');

        // Calculate Total Win Amount (win_lose_status = 1)
        $totalWin = $userTransactions->where('win_lose_status', 1)->sum('transaction_amount');

        // Calculate Total Lose Amount (win_lose_status = 0)
        $totalLose = $userTransactions->where('win_lose_status', 0)
            ->sum(function ($transaction) {
                return abs($transaction->transaction_amount);
            });

        // Format the response data
        $data = [
            'user_id' => $user_id,
            'player_name' => $playerName,
            'total_bet' => $totalBet,
            'total_win' => $totalWin,
            'total_lose' => $totalLose,
            'transactions' => $userTransactions,
        ];

        // Return a JSON success response using the HttpResponses trait
        return $this->success($data, 'Shan Report retrieved successfully');
    }

    public function SystemWalletTest(Request $request) {}
}
