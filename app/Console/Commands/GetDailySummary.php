<?php

namespace App\Console\Commands;

use App\Models\Admin\DailySummary;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetDailySummary extends Command
{
    protected $signature = 'summary:fetch';

    protected $description = 'Fetch daily summary data and store it in the database';

    public function handle()
    {
        $payload = [
            'OperatorId' => 'delightMMK',
            'RequestDateTime' => now()->format('Y-m-d H:i:s'),
            'Signature' => md5('GetDaySummary'.now()->format('Y-m-d H:i:s').'delightMMK'.config('game.api.secret_key')),
            'Date' => now()->subDay()->format('Y-m-d\TH:i:s\Z'), // yesterday's date
        ];

        //$response = Http::post('https://api.sm-sspi-uat.com/api/opgateway/v1/op/GetDaySummary', $payload);

        $response = Http::post('https://api.sm-sspi-prod.com/api/opgateway/v1/op/GetDaySummary', $payload);

        if ($response->successful()) {
            $data = $response->json()['Trans'][0]; // Assuming a single entry in Trans

            DailySummary::create([
                'summary_date' => Carbon::yesterday()->toDateString(),
                'currency_code' => $data['CurrencyCode'],
                'turnover' => $data['Turnover'],
                'valid_turnover' => $data['ValidTurnover'],
                'payout' => $data['Payout'],
                'win_lose' => $data['WinLose'],
            ]);

            $this->info('Daily summary data fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch daily summary data.');
        }
    }
}
