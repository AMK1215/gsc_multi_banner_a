<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeamlessTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from_date' => Carbon::parse($this->from_date)->format('Y-m-d H:i:s'),
            'to_date' => Carbon::parse($this->to_date)->format('Y-m-d H:i:s'),
            'provider_name' => $this->provider_name,
            'total_count' => $this->total_count,
            'total_bet_amount' => number_format($this->total_bet_amount, 2),
            'total_payout_amount' => number_format($this->total_payout_amount, 2),
        ];
    }
}
