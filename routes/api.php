<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\TranData\GetUserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DepositRequestController;
use App\Http\Controllers\Api\V1\GetAdminSiteLogoNameController;
use App\Http\Controllers\Api\V1\Webhook\GetBalanceController;
use App\Http\Controllers\Api\V1\Webhook\PlaceBetController;
use App\Http\Controllers\Api\V1\Webhook\RollbackController;
use App\Http\Controllers\Api\V1\Webhook\GameResultController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetController;
use App\Http\Controllers\Api\V1\Webhook\PushBetController;
use App\Http\Controllers\Api\V1\Webhook\BuyInController;
use App\Http\Controllers\Api\V1\Webhook\BuyOutController;
use App\Http\Controllers\Api\V1\Webhook\BonusController;
use App\Http\Controllers\Api\V1\Webhook\JackPotController;
use App\Http\Controllers\Api\V1\Webhook\MobileLoginController;
use App\Http\Controllers\Api\V1\Monitor\DataVisualizationController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\Shan\ShanTransactionController;
use App\Http\Controllers\Api\V1\Slot\GameController;
use App\Http\Controllers\Api\V1\Game\LaunchGameController;
use App\Http\Controllers\Api\V1\Game\DirectLaunchGameController;

use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WagerController;
use App\Http\Controllers\Api\V1\WithDrawRequestController;
use App\Http\Controllers\Api\Webhook\TestingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


require_once __DIR__.'/user.php';




// sameless route
Route::group(['prefix' => 'Seamless'], function () {
    Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
    Route::post('PlaceBet', [PlaceBetController::class, 'placeBetNew']);
    Route::post('GameResult', [GameResultController::class, 'gameResult']);
    Route::post('Rollback', [RollbackController::class, 'rollback']);
    // Route::group(["middleware" => ["webhook_log"]], function(){
    // Route::post('GetGameList', [LaunchGameController::class, 'getGameList']);
    Route::post('CancelBet', [CancelBetController::class, 'cancelBet']);
    Route::post('BuyIn', [BuyInController::class, 'buyIn']);
    Route::post('BuyOut', [BuyOutController::class, 'buyOut']);
    Route::post('PushBet', [PushBetController::class, 'pushBet']);
    Route::post('Bonus', [BonusController::class, 'bonus']);
    Route::post('Jackpot', [JackPotController::class, 'jackPot']);
    Route::post('MobileLogin', [MobileLoginController::class, 'MobileLogin']);
    // });
});

Route::post('transactions', [ShanTransactionController::class, 'index'])->middleware('transaction');

// for slot
Route::post('/transaction-details/{tranId}', [TransactionController::class, 'getTransactionDetails']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('wager-logs', [WagerController::class, 'index']); //GSC
    Route::get('shan-transactions', [TransactionController::class, 'GetPlayerShanReport']);

    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('contact', [AuthController::class, 'getContact']);
    Route::get('agent', [AuthController::class, 'getAgent']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::get('agentPaymentType', [BankController::class, 'all']);
    Route::post('deposit', [DepositRequestController::class, 'deposit']);
    Route::get('depositlog', [DepositRequestController::class, 'log']);
    Route::get('paymentType', [BankController::class, 'paymentType']);
    Route::post('withdraw', [WithDrawRequestController::class, 'withdraw']);
    Route::post('withdrawTest', [WithDrawRequestController::class, 'withdrawTest']);
    Route::get('withdrawlog', [WithDrawRequestController::class, 'log']);
    Route::get('sitelogo-name', [GetAdminSiteLogoNameController::class, 'GetSiteLogoAndSiteName']);
    Route::get('toptenwithdraw', [BannerController::class, 'TopTen']);


    Route::get('winnerText', [BannerController::class, 'winnerText']);

    Route::get('gameTypeProducts/{id}', [GameController::class, 'gameTypeProducts']);
    Route::get('allGameProducts', [GameController::class, 'allGameProducts']);
    Route::get('game_types', [GameController::class, 'gameType']);
    Route::get('hotgamelist', [GameController::class, 'HotgameList']);
    //Route::get('pphotgamelist', [GameController::class, 'PPHotgameList']);
    Route::get('gamelist/{provider_id}/{game_type_id}/', [GameController::class, 'gameList']);
    //Route::get('slotfishgamelist/{provider_id}/{game_type_id}/', [GameController::class, 'JILIgameList']);
    Route::get('gameFilter', [GameController::class, 'gameFilter']);

    // gsc
    Route::group(['prefix' => 'game'], function () {
        Route::post('Seamless/LaunchGame', [LaunchGameController::class, 'launchGame']);
        Route::get('gamelist/{provider_id}/{game_type_id}', [GameController::class, 'gameList']);
    });

    Route::group(['prefix' => 'direct'], function () {
        Route::post('Seamless/LaunchGame', [DirectLaunchGameController::class, 'launchGame']);
    });
});

// DataVisualize for real time Monitoring
Route::get('/visual-bets', [DataVisualizationController::class, 'VisualizeBet']); // Fetch all bets
Route::get('/visual-results', [DataVisualizationController::class, 'VisualizeResult']); // Fetch all results

Route::get('/getvisualresults', [DataVisualizationController::class, 'getResultsData']); // Fetch all results

// transfer data to second db

Route::group(['prefix' => 'transferdata'], function () {
    // get all user
    Route::get('/getallusers', [GetUserController::class, 'getAllUsers']); // Fetch all users

});

Route::get('/results/user/{userName}', [ReportController::class, 'getResultsForUser']);
