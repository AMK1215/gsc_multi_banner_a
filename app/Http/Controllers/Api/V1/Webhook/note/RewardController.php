<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\RewardWebhoodRequest;
use App\Models\User;
use App\Models\Webhook\Reward;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    use UseWebhook;

    public function handleReward(RewardWebhoodRequest $request): JsonResponse
    {
        $transactions = $request->getTransactions();

        DB::beginTransaction();
        try {
            // Log::info('Starting handleReward method for multiple transactions');

            foreach ($transactions as $transaction) {
                // Retrieve player by PlayerId
                $player = User::where('user_name', $transaction['PlayerId'])->first();
                if (! $player) {
                    Log::warning('Invalid player detected', [
                        'PlayerId' => $transaction['PlayerId'],
                    ]);

                    return $this->buildErrorResponse(StatusCode::InvalidPlayerPassword);
                }

                // Validate signature
                $signature = $this->generateSignature($transaction);
                if ($signature !== $transaction['Signature']) {
                    // Log::warning('Signature validation failed', [
                    //     'transaction' => $transaction,
                    //     'generated_signature' => $signature,
                    // ]);

                    return $this->buildErrorResponse(StatusCode::InvalidSignature);
                }

                // Check for duplicate transaction
                $existingTransaction = Reward::where('tran_id', $transaction['TranId'])->first();
                if ($existingTransaction) {
                    Log::warning('Duplicate TranId detected', [
                        'TranId' => $transaction['TranId'],
                    ]);
                    $balance = $player->wallet->balanceFloat;

                    return $this->buildErrorResponse(StatusCode::DuplicateTransaction, $balance);
                }

                // Process the reward
                $this->processTransfer(
                    User::adminUser(),
                    $player,
                    TransactionName::Bonus,
                    $transaction['Amount']
                );

                $newBalance = $player->wallet->refreshBalance()->balanceFloat;
                // $player->wallet->refreshBalance();
                // $newBalance = $player->wallet->balanceFloat;

                // Create the reward record
                $reward = Reward::create([
                    'user_id' => $player->id,
                    'operator_id' => $transaction['OperatorId'],
                    'request_date_time' => $transaction['RequestDateTime'],
                    'signature' => $transaction['Signature'],
                    'player_id' => $transaction['PlayerId'],
                    'currency' => $transaction['Currency'],
                    'tran_id' => $transaction['TranId'],
                    'reward_id' => $transaction['RewardId'],
                    'reward_name' => $transaction['RewardName'],
                    'amount' => $transaction['Amount'],
                    'tran_date_time' => $transaction['TranDateTime'],
                    'reward_detail' => $transaction['RewardDetail'] ?? null,
                ]);

                Log::info('Reward transaction processed successfully', ['TranId' => $transaction['TranId']]);
                if (! $reward) {
                    throw new \Exception('Failed to create reward record');
                }

            }

            DB::commit();
            // Log::info('All reward transactions committed successfully');

            return $this->buildSuccessResponse($newBalance);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle Reward', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failed to handle Reward'], 500);
        }
    }

    private function buildSuccessResponse(float $newBalance): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Success',
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
            'Balance' => round($newBalance, 4),
        ]);
    }

    private function buildErrorResponse(StatusCode $statusCode, float $balance = 0): JsonResponse
    {
        return response()->json([
            'Status' => $statusCode->value,
            'Description' => $statusCode->name,
            'Balance' => round($balance, 4),
        ]);
    }

    private function generateSignature(array $transaction): string
    {
        $method = 'Reward';

        return md5($method.$transaction['TranId'].$transaction['RequestDateTime'].
                   $transaction['OperatorId'].config('game.api.secret_key').$transaction['PlayerId']);
    }
}

//$newBalance = $player->wallet->refreshBalance()->balanceFloat;
//$request->getMember()->wallet->refreshBalance();

//$newBalance = $request->getMember()->balanceFloat;
