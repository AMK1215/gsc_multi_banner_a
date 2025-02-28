<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiBannerReportController extends Controller
{
    protected const SUB_AGENT_ROlE = 'Sub Agent';

    public function getSeniorReport(Request $request)
    {
        $seniorId = auth()->id();

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $admins = User::with([
            'agents.players.results' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'agents.players.betNResults' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        ])
            ->where('agent_id', $seniorId)
            ->get();

        $data = [];
        foreach ($admins as $admin) {
            $totalBets = 0;
            $totalWins = 0;
            $totalNet = 0;

            foreach ($admin->agents as $agent) {
                foreach ($agent->players as $player) {

                    $totalBets += $player->results->sum('total_bet_amount') + $player->betNResults->sum('bet_amount');
                    $totalWins += $player->results->sum('win_amount') + $player->betNResults->sum('win_amount');
                    $totalNet += $player->results->sum('net_win') + $player->betNResults->sum('net_win');
                }
            }

            $data[] = [
                'admin_name' => $admin->name,
                'total_bets' => $totalBets,
                'total_wins' => $totalWins,
                'total_net' => $totalNet,
            ];
        }

        return view('admin.reports.senior.index', compact('data', 'admins'));
    }

    public function getOwnerReport(Request $request)
    {
        $ownerId = auth()->id();

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $agents = User::with([
            'players.results' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'players.betNResults' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        ])
            ->where('agent_id', $ownerId)
            ->get();

        $data = [];

        foreach ($agents as $agent) {
            $totalBets = 0;
            $totalWins = 0;
            $totalNet = 0;
            foreach ($agent->players as $player) {
                $totalBets += $player->results->sum('total_bet_amount') + $player->betNResults->sum('bet_amount');
                $totalWins += $player->results->sum('win_amount') + $player->betNResults->sum('win_amount');
                $totalNet += $player->results->sum('net_win') + $player->betNResults->sum('net_win');
            }

            $data[] = [
                'agent_name' => $agent->name,
                'total_bets' => $totalBets,
                'total_wins' => $totalWins,
                'total_net' => $totalNet,
            ];
        }

        return view('admin.reports.owner.index', compact('data'));
    }

    public function getAgentReport(Request $request)
    {
        $agent = $this->getAgent() ?? Auth::user();

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $players = User::with([
            'results' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            },
            'betNResults' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        ])
            ->where('agent_id', $agent->id)
            ->get();

        $data = [];

        foreach ($players as $player) {
            $totalBets = 0;
            $totalWins = 0;
            $totalNet = 0;
            $totalBets += $player->results->sum('total_bet_amount') + $player->betNResults->sum('bet_amount');
            $totalWins += $player->results->sum('win_amount') + $player->betNResults->sum('win_amount');
            $totalNet += $player->results->sum('net_win') + $player->betNResults->sum('net_win');


            $data[] = [
                'player_name' => $player->name,
                'total_bets' => $totalBets,
                'total_wins' => $totalWins,
                'total_net' => $totalNet,
            ];
        }
        return view('admin.reports.agent.index', compact('data'));
    }

    public function getAgentDetail($userId)
    {
        $player = User::findOrFail($userId);

        $details = Result::where('user_id', $userId)->get();

        return view('admin.reports.agent.detail', compact('player', 'details'));
    }

    private function isExistingAgent($userId)
    {
        $user = User::find($userId);

        return $user && $user->hasRole(self::SUB_AGENT_ROlE) ? $user->parent : null;
    }

    private function getAgent()
    {
        return $this->isExistingAgent(Auth::id());
    }
}
