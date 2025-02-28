<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ArchiveOldResult extends Command
{
    protected $signature = 'archive:old-result';

    protected $description = 'Archive and delete old results in chunks';

    public function handle()
    {
        // Define the date range from the start of the previous day to now
        // $startOfDay = now()->subDays(2)->startOfDay();
        // $endOfDay = now();
        // Define the date range from 2025-02-02 00:00:00 to 2025-02-04 23:59:59
    $startOfDay = Carbon::create(2025, 2, 2, 0, 0, 0);
    $endOfDay = Carbon::create(2025, 2, 4, 23, 59, 59);
        // $startOfDay = now()->setTime(8, 30, 0); // Today at 9:00 AM
        // $endOfDay = now()->setTime(10, 0, 0); // Today at 10:00 AM

        try {
            DB::table('results')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderBy('id')
                ->chunk(1000, function ($oldResults) {
                    if ($oldResults->isEmpty()) {
                        $this->info('No results found to archive.');

                        return;
                    }

                    $this->info(count($oldResults).' results found for archiving.');

                    DB::transaction(function () use ($oldResults) {
                        // Insert old results into the result_backups table in smaller batches
                        $oldResults->chunk(100)->each(function ($batch) {
                            try {
                                DB::table('result_backups')->insert(
                                    $batch->map(function ($result) {
                                        return [
                                            'user_id' => $result->user_id,
                                            'player_name' => $result->player_name,
                                            'game_provide_name' => $result->game_provide_name,
                                            'game_name' => $result->game_name,
                                            'operator_id' => $result->operator_id,
                                            'request_date_time' => $result->request_date_time,
                                            'signature' => $result->signature,
                                            'player_id' => $result->player_id,
                                            'currency' => $result->currency,
                                            'round_id' => $result->round_id,
                                            'bet_ids' => $result->bet_ids,
                                            'result_id' => $result->result_id,
                                            'game_code' => $result->game_code,
                                            'total_bet_amount' => $result->total_bet_amount,
                                            'win_amount' => $result->win_amount,
                                            'net_win' => $result->net_win,
                                            'tran_date_time' => $result->tran_date_time,
                                            'created_at' => $result->created_at,
                                            'updated_at' => $result->updated_at,
                                        ];
                                    })->toArray()
                                );
                            } catch (\Exception $e) {
                                Log::error('Error inserting results into result_backups: '.$e->getMessage());
                                $this->error('Failed to insert some results. Check logs for details.');
                            }
                        });

                        // Disable foreign key checks for deletion
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                        try {
                            $resultIds = $oldResults->pluck('id')->toArray();
                            DB::table('results')->whereIn('id', $resultIds)->delete();
                        } catch (\Exception $e) {
                            Log::error('Error deleting old results: '.$e->getMessage());
                            $this->error('Failed to delete some old results. Check logs for details.');
                        }

                        // Re-enable foreign key checks
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    });

                    $this->info(count($oldResults).' results have been archived and deleted successfully.');
                });

            $this->info('Result archiving complete.');
        } catch (\Exception $e) {
            Log::error('Error archiving results: '.$e->getMessage());
            $this->error('An error occurred while archiving results. Check logs for details.');
        }
    }
}