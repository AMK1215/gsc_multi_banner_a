<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveOldBetNResult extends Command
{
    protected $signature = 'archive:old-bet-n-result';

    protected $description = 'Archive and delete old bet_n_results in chunks';

    public function handle()
    {
        // Define the date range from the start of the previous day to now
        $startOfDay = now()->subDays(2)->startOfDay();
        $endOfDay = now();
        // $startOfDay = now()->setTime(8, 30, 0); // Today at 9:00 AM
        // $endOfDay = now()->setTime(10, 0, 0); // Today at 10:00 AM

        try {
            DB::table('bet_n_results')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderBy('id')
                ->chunk(1000, function ($oldResults) {
                    if ($oldResults->isEmpty()) {
                        $this->info('No bet_n_results found to archive.');

                        return;
                    }

                    $this->info(count($oldResults).' bet_n_results found for archiving.');

                    DB::transaction(function () use ($oldResults) {
                        // Insert old results into the result_backups table in smaller batches
                        $oldResults->chunk(100)->each(function ($batch) {
                            try {
                                DB::table('betresult_backups')->insert(
                                    $batch->map(function ($result) {
                                        return [
                                            'user_id' => $result->user_id,
                                            'operator_id' => $result->operator_id,
                                            'request_date_time' => $result->request_date_time,
                                            'signature' => $result->signature,
                                            'player_id' => $result->player_id,
                                            'currency' => $result->currency,
                                            'tran_id' => $result->tran_id,
                                            'game_code' => $result->game_code,
                                            'bet_amount' => $result->bet_amount,
                                            'win_amount' => $result->win_amount,
                                            'net_win' => $result->net_win,
                                            'tran_date_time' => $result->tran_date_time,
                                            'provider_code' => $result->provider_code,
                                            'auth_token' => $result->auth_token,
                                            'status' => $result->status,
                                            'cancelled_at' => $result->cancelled_at,
                                            'old_balance' => $result->old_balance,
                                            'new_balance' => $result->new_balance,
                                            'created_at' => $result->created_at,
                                            'updated_at' => $result->updated_at,
                                        ];
                                    })->toArray()
                                );
                            } catch (\Exception $e) {
                                Log::error('Error inserting BetNresults into result_backups: '.$e->getMessage());
                                $this->error('Failed to insert some BetNresults. Check logs for details.');
                            }
                        });

                        // Disable foreign key checks for deletion
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                        try {
                            $resultIds = $oldResults->pluck('id')->toArray();
                            DB::table('results')->whereIn('id', $resultIds)->delete();
                        } catch (\Exception $e) {
                            Log::error('Error deleting old BetNresults: '.$e->getMessage());
                            $this->error('Failed to delete some old BetNresults. Check logs for details.');
                        }

                        // Re-enable foreign key checks
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    });

                    $this->info(count($oldResults).' results have been archived and deleted successfully.');
                });

            $this->info('BetNResult archiving complete.');
        } catch (\Exception $e) {
            Log::error('Error archiving BetNresults: '.$e->getMessage());
            $this->error('An error occurred while archiving BetNresults. Check logs for details.');
        }
    }
}
