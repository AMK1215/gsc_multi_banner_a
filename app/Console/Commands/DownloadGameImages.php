<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadGameImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:game-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download images for games from a JSON file and save them with their respective game names';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Path to the JSON file
        $jsonFilePath = base_path('database/seeders/data/HSW.json');

        // Check if the JSON file exists
        if (! File::exists($jsonFilePath)) {
            $this->error('JSON file not found at '.$jsonFilePath);

            return 1;
        }

        // Read the JSON file
        $jsonData = File::get($jsonFilePath);
        $gamesData = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Error decoding JSON: '.json_last_error_msg());

            return 1;
        }

        // Access the games from the JSON file
        $games = $gamesData['Game']; // Adjusted for the correct JSON structure

        // Create directory to store images
        $directoryPath = public_path('assets/img/game_list/hsw');
        if (! File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Loop through games and download images
        foreach ($games as $game) {
            $this->downloadImage($game['ImageUrl'], $game['GameName'], $directoryPath);
        }

        $this->info('All images have been downloaded.');

        return 0;
    }

    // public function handle()
    // {
    //     // Path to the JSON file
    //     $jsonFilePath = base_path('database/seeders/data/PragmaticPlaySlot.json');

    //     // Check if the JSON file exists
    //     if (! File::exists($jsonFilePath)) {
    //         $this->error('JSON file not found at '.$jsonFilePath);

    //         return 1;
    //     }

    //     // Read the JSON file
    //     $jsonData = File::get($jsonFilePath);
    //     $gamesData = json_decode($jsonData, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         $this->error('Error decoding JSON: '.json_last_error_msg());

    //         return 1;
    //     }

    //     // Access the games from the JSON file
    //     $games = $gamesData['data'];

    //     // Create directory to store images
    //     $directoryPath = public_path('assets/img/game_list/pplay');
    //     if (! File::exists($directoryPath)) {
    //         File::makeDirectory($directoryPath, 0755, true);
    //     }

    //     // Loop through games and download images
    //     foreach ($games as $game) {
    //         $this->downloadImage($game['image_url'], $game['name'], $directoryPath);
    //     }

    //     $this->info('All images have been downloaded.');

    //     return 0;
    // }

    /**
     * Download the image and save it with the game name.
     *
     * @param  string  $url
     * @param  string  $gameName
     * @param  string  $directory
     * @return void
     */
    private function downloadImage($url, $gameName, $directory)
    {
        $fileName = str_replace(' ', '_', $gameName).'.png';
        $filePath = $directory.'/'.$fileName;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (use with caution)
        $imageContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode == 200 && $imageContent) {
            File::put($filePath, $imageContent);
            $this->info($fileName.' downloaded successfully.');
        } else {
            $this->error('Failed to download image for '.$gameName.'. Error: '.$error);
        }
    }

    //     private function downloadImage($url, $gameName, $directory)
    // {
    //     $fileName = str_replace(' ', '_', $gameName) . '.png';
    //     $filePath = $directory . '/' . $fileName;

    //     try {
    //         $imageContent = file_get_contents($url);
    //         if ($imageContent !== false) {
    //             File::put($filePath, $imageContent);
    //             $this->info($fileName . ' downloaded successfully.');
    //         } else {
    //             $this->error('Failed to download image for ' . $gameName);
    //         }
    //     } catch (\Exception $e) {
    //         $this->error('Exception occurred for ' . $gameName . ': ' . $e->getMessage());
    //     }
    // }

    // private function downloadImage($url, $gameName, $directory)
    // {
    //    // $response = Http::get($url);
    //    //$response = Http::timeout(30)->withoutVerifying()->get($url);
    //     $response = Http::retry(3, 1000)->timeout(30)->get($url);

    //     if ($response->successful()) {
    //         $fileName = str_replace(' ', '_', $gameName).'.png';
    //         File::put($directory.'/'.$fileName, $response->body());
    //         $this->info($fileName.' downloaded successfully.');
    //     } else {
    //         $this->error('Failed to download image for '.$gameName);
    //     }
    // }
}
