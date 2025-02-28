<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositLogResource;
use App\Models\DepositRequest;
use App\Models\User;
use App\Notifications\PlayerDepositNotification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DepositRequestController extends Controller
{
    use HttpResponses;

    public function deposit(Request $request)
    {
        $request->validate([
            'agent_payment_type_id' => ['required', 'integer'],
            'amount' => ['required', 'integer', 'min: 1000'],
            'refrence_no' => ['required', 'digits:6'],
        ]);
        $player = Auth::user();
        $image = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid('deposit') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/deposit/'), $filename);
        }
        
        $depositData = [
            'agent_payment_type_id' => $request->agent_payment_type_id,
            'user_id' => $player->id,
            'agent_id' => $player->agent_id,
            'amount' => $request->amount,
            'refrence_no' => $request->refrence_no,
        ];
        
        if ($image) {
            $depositData['image'] = $filename;
        }
        
        $deposit = DepositRequest::create($depositData);
        
        // $admins = User::where('type', '30')->get();
        // Notification::send($admins, new PlayerDepositNotification($deposit));
        // Notify the player's agent
        $agent = User::find($player->agent_id);
        if ($agent) {
            $agent->notify(new PlayerDepositNotification($deposit));
        }

        return $this->success($deposit, 'Deposit Request Success');

    }

    public function log()
    {
        $deposit = DepositRequest::with('bank')->where('user_id', Auth::id())->get();

        return $this->success(DepositLogResource::collection($deposit));
    }
}
