<?php

use App\Http\Controllers\Admin\AdsVedioController;
use App\Http\Controllers\Admin\Agent\AgentController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BannerAds\BannerAdsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\Bonu\BonusController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DailySummaryController;
use App\Http\Controllers\Admin\Deposit\DepositRequestController;
use App\Http\Controllers\Admin\GameListController;
use App\Http\Controllers\Admin\GameListImageURLUpdateController;
use App\Http\Controllers\Admin\GameTypeProductController;
use App\Http\Controllers\Admin\GetBetDetailController;
use App\Http\Controllers\Admin\GSCReportController;
use App\Http\Controllers\Admin\MultiBannerReportController;
use App\Http\Controllers\Admin\NewGameListController;
use App\Http\Controllers\Admin\Owner\OwnerController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\Player\PlayerController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\Seniors\SeniorHierarchyController;
use App\Http\Controllers\Admin\Shan\ShanReportController;
use App\Http\Controllers\Admin\SubAccountController;
use App\Http\Controllers\Admin\TopTenWithdrawController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransferLog\TransferLogController;
use App\Http\Controllers\Admin\WinnerTextController;
use App\Http\Controllers\Admin\WithDraw\WithDrawRequestController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewReportController;
use App\Http\Controllers\ReportV2Controller;
use App\Http\Controllers\ResultArchiveController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'checkBanned'],
], function () {

    Route::post('balance-up', [HomeController::class, 'balanceUp'])->name('balanceUp');
    Route::get('logs/{id}', [HomeController::class, 'logs'])
        ->name('logs');

    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);

    Route::get('/changePassword/{user}', [HomeController::class, 'changePassword'])->name('changePassword');
    Route::post('/updatePassword/{user}', [HomeController::class, 'updatePassword'])->name('updatePassword');

    Route::get('/changeplayersite/{user}', [HomeController::class, 'changePlayerSite'])->name('changeSiteName');

    Route::post('/updatePlayersite/{user}', [HomeController::class, 'updatePlayerSiteLink'])->name('updateSiteLink');

    Route::get('/player-list', [HomeController::class, 'playerList'])->name('playerList');

    // Players
    Route::delete('user/destroy', [PlayerController::class, 'massDestroy'])->name('user.massDestroy');
    Route::put('player/{id}/ban', [PlayerController::class, 'banUser'])->name('player.ban');
    Route::resource('player', PlayerController::class);
    Route::get('player-cash-in/{player}', [PlayerController::class, 'getCashIn'])->name('player.getCashIn');
    Route::post('player-cash-in/{player}', [PlayerController::class, 'makeCashIn'])->name('player.makeCashIn');
    Route::get('player/cash-out/{player}', [PlayerController::class, 'getCashOut'])->name('player.getCashOut');
    Route::post('player/cash-out/update/{player}', [PlayerController::class, 'makeCashOut'])
        ->name('player.makeCashOut');
    Route::get('player-changepassword/{id}', [PlayerController::class, 'getChangePassword'])->name('player.getChangePassword');
    Route::post('player-changepassword/{id}', [PlayerController::class, 'makeChangePassword'])->name('player.makeChangePassword');
    Route::get('/players-list', [PlayerController::class, 'player_with_agent'])->name('playerListForAdmin');

    Route::resource('banners', BannerController::class);

    Route::resource('video-upload', AdsVedioController::class);

    Route::resource('adsbanners', BannerAdsController::class);
    Route::resource('text', BannerTextController::class);
    Route::resource('winner_text', WinnerTextController::class);
    Route::resource('/promotions', PromotionController::class);
    Route::resource('contact', ContactController::class);
    Route::resource('paymentTypes', PaymentTypeController::class);
    Route::resource('bank', BankController::class);

    // provider Game Type Start
    Route::get('gametypes', [GameTypeProductController::class, 'index'])->name('gametypes.index');
    Route::get('gametypes/{game_type_id}/product/{product_id}', [GameTypeProductController::class, 'edit'])->name('gametypes.edit');
    Route::post('gametypes/{game_type_id}/product/{product_id}', [GameTypeProductController::class, 'update'])->name('gametypes.update');
    // provider Game Type End

    Route::post('/mark-notifications-read', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    })->name('markNotificationsRead');

    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');


    Route::get('transaction-list', [TransactionController::class, 'index'])->name('transaction');
    // game list start
    Route::get('all-game-lists', [GameListController::class, 'index'])->name('gameLists.index');
    Route::get('all-game-lists/{id}', [GameListController::class, 'edit'])->name('gameLists.edit');
    Route::post('all-game-lists/{id}', [GameListController::class, 'update'])->name('gameLists.update');

    Route::patch('gameLists/{id}/toggleStatus', [GameListController::class, 'toggleStatus'])->name('gameLists.toggleStatus');

    Route::patch('hotgameLists/{id}/toggleStatus', [GameListController::class, 'HotGameStatus'])->name('HotGame.toggleStatus');

    // pp hot

    Route::patch('pphotgameLists/{id}/toggleStatus', [GameListController::class, 'PPHotGameStatus'])->name('PPHotGame.toggleStatus');

    Route::get('game-list/{gameList}/edit', [GameListImageURLUpdateController::class, 'edit'])->name('game_list.edit');
    Route::post('/game-list/{id}/update-image-url', [GameListImageURLUpdateController::class, 'updateImageUrl'])->name('game_list.update_image_url');
    Route::get('game-list-order/{gameList}/edit', [GameListController::class, 'GameListOrderedit'])->name('game_list_order.edit');
    Route::post('/game-lists/{id}/update-order', [GameListController::class, 'updateOrder'])->name('GameListOrderUpdate');
    Route::get('/game-lists/search', [GameListController::class, 'searchGames'])->name('gameLists.search');
    Route::get('/game-lists-search', [GameListController::class, 'GetsearchGames'])->name('gameLists.search_index');
    Route::post('/game-lists/updateordercolumn', [GameListController::class, 'updateAllOrder'])->name('gameLists.updateOrder');

    Route::resource('gamelistnew', NewGameListController::class);

    // game list end
    Route::resource('agent', AgentController::class);
    Route::get('agent-cash-in/{id}', [AgentController::class, 'getCashIn'])->name('agent.getCashIn');
    Route::post('agent-cash-in/{id}', [AgentController::class, 'makeCashIn'])->name('agent.makeCashIn');
    Route::get('agent/cash-out/{id}', [AgentController::class, 'getCashOut'])->name('agent.getCashOut');
    Route::post('agent/cash-out/update/{id}', [AgentController::class, 'makeCashOut'])
        ->name('agent.makeCashOut');
    Route::put('agent/{id}/ban', [AgentController::class, 'banAgent'])->name('agent.ban');
    Route::get('agent-changepassword/{id}', [AgentController::class, 'getChangePassword'])->name('agent.getChangePassword');
    Route::post('agent-changepassword/{id}', [AgentController::class, 'makeChangePassword'])->name('agent.makeChangePassword');
    Route::resource('subacc', SubAccountController::class);
    Route::resource('owner', OwnerController::class);
    Route::put('subacc/{id}/ban', [SubAccountController::class, 'banSubAcc'])->name('subacc.ban');
    Route::get('subacc-changepassword/{id}', [SubAccountController::class, 'getChangePassword'])->name('subacc.getChangePassword');
    Route::post('subacc-changepassword/{id}', [SubAccountController::class, 'makeChangePassword'])->name('subacc.makeChangePassword');

    Route::get('owner-player-list', [OwnerController::class, 'OwnerPlayerList'])->name('GetOwnerPlayerList');
    Route::get('owner-cash-in/{id}', [OwnerController::class, 'getCashIn'])->name('owner.getCashIn');
    Route::post('owner-cash-in/{id}', [OwnerController::class, 'makeCashIn'])->name('owner.makeCashIn');
    Route::get('mastownerer/cash-out/{id}', [OwnerController::class, 'getCashOut'])->name('owner.getCashOut');
    Route::post('owner/cash-out/update/{id}', [OwnerController::class, 'makeCashOut'])
        ->name('owner.makeCashOut');
    Route::put('owner/{id}/ban', [OwnerController::class, 'banOwner'])->name('owner.ban');
    Route::get('owner-changepassword/{id}', [OwnerController::class, 'getChangePassword'])->name('owner.getChangePassword');
    Route::post('owner-changepassword/{id}', [OwnerController::class, 'makeChangePassword'])->name('owner.makeChangePassword');

    Route::get('agent-to-player-deplogs', [AgentController::class, 'AgentToPlayerDepositLog'])->name('agent.AgentToPlayerDepLog');

    Route::get('agent-win-lose-report', [AgentController::class, 'AgentWinLoseReport'])->name('agent.AgentWinLose');

    Route::get('/agent/wldetails/{agent_id}/{month}', [AgentController::class, 'AgentWinLoseDetails'])->name('agent_winLdetails');

    Route::get('auth-agent-win-lose-report', [AgentController::class, 'AuthAgentWinLoseReport'])->name('AuthAgentWinLose');

    Route::get('/authagent/wldetails/{agent_id}/{month}', [AgentController::class, 'AuthAgentWinLoseDetails'])->name('authagent_winLdetails');

    Route::get('/agent-to-player-detail/{agent_id}/{player_id}', [AgentController::class, 'AgentToPlayerDetail'])->name('agent.to.player.detail');

    Route::get('withdraw', [WithDrawRequestController::class, 'index'])->name('agent.withdraw');
    Route::post('withdraw/{withdraw}', [WithDrawRequestController::class, 'statusChangeIndex'])->name('agent.withdrawStatusUpdate');
    Route::post('withdraw/reject/{withdraw}', [WithDrawRequestController::class, 'statusChangeReject'])->name('agent.withdrawStatusreject');

    //Route::group(['prefix' => 'report'], function () {
    Route::get('slot-win-lose', [GSCReportController::class, 'index'])->name('GscReport.index');

    Route::get('/win-lose/details/{product_name}', [GSCReportController::class, 'ReportDetails'])->name('Reportproduct.details');

    Route::get('agent-slot-win-lose', [GSCReportController::class, 'AgentWinLoseindex'])->name('GscReport.AgentWLindex');

    Route::get('shan-report', [ShanReportController::class, 'index'])->name('shan.reports.index');
    Route::get('shan-reports/{user_id}', [ShanReportController::class, 'show'])->name('shanreport.show');
    // for agent shan report
    Route::get('agent-shan-report', [ShanReportController::class, 'ShanAgentReportIndex'])->name('shanreports_index');

    Route::get('deposit', [DepositRequestController::class, 'index'])->name('agent.deposit');
    Route::get('deposit/{deposit}', [DepositRequestController::class, 'view'])->name('agent.depositView');
    Route::post('deposit/{deposit}', [DepositRequestController::class, 'statusChangeIndex'])->name('agent.depositStatusUpdate');
    Route::post('deposit/reject/{deposit}', [DepositRequestController::class, 'statusChangeReject'])->name('agent.depositStatusreject');

    Route::get('transer-log', [TransferLogController::class, 'index'])->name('transferLog');
    Route::get('transferlog/{id}', [TransferLogController::class, 'transferLog'])->name('transferLogDetail');
    Route::get('top-10-withdraw-log', [TransferLogController::class, 'getTopWithdrawals'])->name('TopTenWithdraw');

    Route::resource('top-10-withdraws', TopTenWithdrawController::class);

    Route::group(['prefix' => 'bonu'], function () {
        Route::get('countindex', [BonusController::class, 'index'])->name('bonu_count.index');
    });

    // get bet deatil
    Route::get('get-bet-detail', [GetBetDetailController::class, 'index'])->name('getBetDetail');
    Route::get('get-bet-detail/{wagerId}', [GetBetDetailController::class, 'getBetDetail'])->name('getBetDetail.show');

    Route::resource('/product_code', App\Http\Controllers\Admin\ProductCodeController::class);

    Route::group(['prefix' => 'slot'], function () {

        Route::get('/game-report', [NewReportController::class, 'getGameReport'])->name('game.report');
        Route::get('/agent-game-report', [NewReportController::class, 'getGameAgentReport'])->name('agent.game.report');
        Route::get('/game-report/{player_id}/{game_code}', [NewReportController::class, 'getGameReportDetail'])->name('game.report.detail');

        Route::get('report', [ReportController::class, 'index'])->name('report.index');
        Route::get('reports/details/{player_id}', [ReportController::class, 'getReportDetails'])->name('reports.details');
        Route::get('adminreport', [ReportController::class, 'Reportindex'])->name('report.adminindex');
        Route::get('reports/player/{playerId}', [ReportController::class, 'playerDetails'])->name('reports.player.details');

        Route::get('agentreport', [ReportController::class, 'AgentReportindex'])->name('report.agentindex');

        Route::get('/daily-summaries', [DailySummaryController::class, 'index'])->name('daily_summaries.index');

        Route::get('/reports/senior', [MultiBannerReportController::class, 'getSeniorReport'])->name('reports.senior');
        Route::get('/reports/owner', [MultiBannerReportController::class, 'getOwnerReport'])->name('reports.owner');
        Route::get('/reports/agent', [MultiBannerReportController::class, 'getAgentReport'])->name('reports.agent');
        Route::get('/reports/agent/detail/{user_id}', [MultiBannerReportController::class, 'getAgentDetail'])->name('reports.agent.detail');

        // senior result
        Route::get('/seniorresults', [NewReportController::class, 'getAllResults'])->name('senior_results.index');
        Route::post('/senior/delete-results', [NewReportController::class, 'deleteResults'])->name('senior.deleteResults');

        Route::get('/seniorbets', [NewReportController::class, 'getAllBets'])->name('senior_bet.index');
        Route::post('/senior/delete-bets', [NewReportController::class, 'deleteBets'])->name('senior.deleteBets');

        Route::get('/seniorbetnresults', [NewReportController::class, 'getAllJili'])->name('senior_bet_n_result.index');
        Route::post('/senior/delete-betnresults', [NewReportController::class, 'deleteJili'])->name('senior.deleteBetNResult');
        // report v3
        Route::get('/results/user/{userName}', [ReportController::class, 'getResultsForOnlyUser']);

        // find by username
        Route::get('/result-search', [ReportController::class, 'GetResult'])->name('ResultSearchIindex');
        Route::post('/results/search', [ReportController::class, 'FindByUserName'])->name('results.search');
        //Route::delete('/results/{id}/delete', [ReportController::class, 'deleteResult'])->name('results.delete');
        Route::delete('/admin/results/deleteMultiple', [ReportController::class, 'deleteMultiple'])->name('results.deleteMultiple');
        Route::delete('/admin/results/{id}', [ReportController::class, 'deleteResult'])->name('results.delete');

        // senior hierarchy
        Route::get('/hierarchy', [SeniorHierarchyController::class, 'GetSeniorHierarchy'])->name('SeniorHierarchy');
        Route::get('/get-owners', [SeniorHierarchyController::class, 'getAllOwners'])->name('GetAllOwners');
        Route::get('/owner/{id}/agents', [SeniorHierarchyController::class, 'getOwnerWithAgents'])->name('OwnerAgentDetail');

        Route::get('/owner/{owner_id}/agent/{agent_id}', [SeniorHierarchyController::class, 'getAgentDetails'])->name('AgentBalanceDetail');

        Route::get('/agent/{id}/players', [SeniorHierarchyController::class, 'getAgentWithPlayers'])->name('AgentPlayerDetail');

    });

    Route::group(['prefix' => 'reportv2'], function () {
        //v2 with backup
        Route::get('v2index', [ReportV2Controller::class, 'index'])->name('reportv2.index');
        Route::get('/detail/{playerId}', [ReportV2Controller::class, 'getReportDetails'])->name('reportv2.detail');
    });

    // report backup
     Route::get('/resultsdata', [ResultArchiveController::class, 'getAllResults'])->name('backup_results.index');
     Route::post('/archive-results', [ResultArchiveController::class, 'archiveResults'])->name('archive.results');

     Route::get('/betNresultsdata', [ResultArchiveController::class, 'getAllBetNResults'])->name('backup_bet_n_results.index');

     Route::post('/archive-betNresults', [ResultArchiveController::class, 'archiveBetNResults'])->name('archive.bet_n_result');
});

Route::get('bo-report-sm', [ReportController::class, 'BoReport'])->name('SmBoReport');