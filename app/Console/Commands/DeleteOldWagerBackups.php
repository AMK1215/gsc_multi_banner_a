<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteOldWagerBackups extends Command
{
    // Define the command signature
    protected $signature = 'result:delete-old-backups';

    // Command description
    protected $description = 'Delete old result from the results table';

    public function handle()
    {
        // Define the date range (start of the day to the current time)
        // $startOfDay = now()->subDays(1)->startOfDay();  // Start of the day 1 day ago
        // $endOfDay = now();  // Current time

        $startOfDay = '2024-11-18';  // Start of the day 1 day ago
        $endOfDay = '2024-12-02';

        DB::table('results')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])  // Delete records between startOfDay and now
            ->orderBy('id')  // Ensure stable sorting to avoid missing records
            ->chunk(1000, function ($oldWagers) {
                if ($oldWagers->isEmpty()) {
                    $this->info('No Result found to delete.');

                    return;
                }

                $this->info(count($oldWagers).' Result found for deletion.');

                DB::transaction(function () use ($oldWagers) {
                    // Fetch the IDs of the old wagers
                    $wagerIds = $oldWagers->pluck('id')->toArray();

                    // Disable and re-enable foreign key checks
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    DB::table('results')->whereIn('id', $wagerIds)->delete();
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                    $this->info(count($oldWagers).' Result have been deleted successfully.');
                });
            });
    }
}
