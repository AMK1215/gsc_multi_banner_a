<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteBetData extends Command
{
    protected $signature = 'bet:delete-old-backups {start_date} {end_date}';

    protected $description = 'Delete old bets from the bets table within a specific date range';

    public function handle()
    {
        $startDate = $this->argument('start_date');
        $endDate = $this->argument('end_date');

        $this->info("Deleting bet between $startDate and $endDate");

        DB::table('bets')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id')
            ->chunk(100, function ($oldWagers) {
                if ($oldWagers->isEmpty()) {
                    $this->info('No bet found to delete.');

                    return;
                }

                DB::transaction(function () use ($oldWagers) {
                    $wagerIds = $oldWagers->pluck('id')->toArray();

                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    DB::table('bets')->whereIn('id', $wagerIds)->delete();
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                });

                $this->info(count($oldWagers).' bets have been deleted.');
            });

        $this->info('bets deletion completed successfully.');
    }
}
