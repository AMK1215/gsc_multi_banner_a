<?php
namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\SlotWebhookResponseCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\SlotWebhookRequest;
use App\Services\Slot\SlotWebhookService;
use App\Services\Slot\SlotWebhookValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetBalanceController extends Controller
{
    public function getBalance(SlotWebhookRequest $request)
    {
        Log::info('GetBalanceController: Received request', ['request' => $request->all()]);

        DB::beginTransaction();
        try {
            Log::info('GetBalanceController: Validating request');

            $validator = SlotWebhookValidator::make($request)->validate();
            if ($validator->fails()) {
                Log::warning('GetBalanceController: Validation failed', ['errors' => $validator->getResponse()]);
                return $validator->getResponse();
            }

            // if ($validator->fails()) {
            //     Log::warning('GetBalanceController: Validation failed', ['errors' => $validator->errors()]);
            //     return $validator->getResponse();
            // }

            $balance = $request->getMember()->balanceFloat;

            Log::info('GetBalanceController: Balance fetched successfully', [
                'member_id' => $request->getMember()->id,
                'balance' => $balance,
            ]);

            DB::commit();

            return SlotWebhookService::buildResponse(
                SlotWebhookResponseCode::Success,
                $balance,
                $balance
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('GetBalanceController: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}


// namespace App\Http\Controllers\Api\V1\Webhook;

// use App\Enums\SlotWebhookResponseCode;
// use App\Http\Controllers\Controller;
// use App\Http\Requests\Slot\SlotWebhookRequest;
// use App\Services\Slot\SlotWebhookService;
// use App\Services\Slot\SlotWebhookValidator;
// use Illuminate\Support\Facades\DB;

// class GetBalanceController extends Controller
// {
//     public function getBalance(SlotWebhookRequest $request)
//     {
//         DB::beginTransaction();
//         try {
//             $validator = SlotWebhookValidator::make($request)->validate();

//             if ($validator->fails()) {
//                 return $validator->getResponse();
//             }

//             $balance = $request->getMember()->balanceFloat;

//             DB::commit();

//             return SlotWebhookService::buildResponse(
//                 SlotWebhookResponseCode::Success,
//                 $balance,
//                 $balance
//             );
//         } catch (\Exception $e) {
//             DB::rollBack();

//             return response()->json([
//                 'message' => $e->getMessage(),
//             ]);
//         }
//     }
// }