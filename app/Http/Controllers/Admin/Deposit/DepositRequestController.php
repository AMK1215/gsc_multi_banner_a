<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositRequestController extends Controller
{
    protected const SUB_AGENT_ROlE = 'Sub Agent';

    public function index(Request $request)
    {
        $agent = $this->getAgent() ?? Auth::user();
        [$startDate, $endDate] = $this->parseDateRange($request);

        $deposits = $this->getQuery($agent, $request, $startDate, $endDate)->get();
        $depositTotal = $this->getQuery($agent, $request, $startDate, $endDate)->sum('amount');

        return view('admin.deposit_request.index', compact('deposits', 'depositTotal'));
    }

    public function statusChangeIndex(Request $request, DepositRequest $deposit)
    {
        try {
            $agent = $this->getAgent() ?? Auth::user();

            $player = User::find($request->player);

            if ($request->status == 1 && $agent->balanceFloat < $request->amount) {
                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            $deposit->update([
                'status' => $request->status,
            ]);

            if ($request->status == 1) {
                app(WalletService::class)->transfer($agent, $player, $request->amount,
                    TransactionName::CreditTransfer, [
                        'old_balance' => $player->balanceFloat,
                        'new_balance' => $player->balanceFloat + $request->amount,
                    ]
                );
            }

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusChangeReject(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            // Update the deposit status
            $deposit->update([
                'status' => $request->status,
            ]);

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function view(DepositRequest $deposit)
    {
        return view('admin.deposit_request.view', compact('deposit'));
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

    private function parseDateRange(Request $request): array
    {
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();

        return [$startDate->format('Y-m-d H:i'), $endDate->format('Y-m-d H:i')];
    }

    private function getQuery($agent, $request, $startDate, $endDate)
    {
        return DepositRequest::with(['user', 'bank', 'agent'])
            ->where('agent_id', $agent->id)
            ->when($request->filled('status') && $request->input('status') !== 'all', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc');
    }
}
