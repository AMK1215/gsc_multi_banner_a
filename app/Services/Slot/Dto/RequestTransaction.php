<?php

namespace App\Services\Slot\Dto;

use Spatie\LaravelData\Data;

class RequestTransaction extends Data
{
    public function __construct(
        //public int $user_id,
        public int $Status,
        public string $ProductID,
        public int $GameType,
        public ?string $TransactionID,
        public ?string $WagerID,
        public ?float $BetAmount,
        public ?float $TransactionAmount,
        public ?float $PayoutAmount,
        public ?float $ValidBetAmount,
        //public ?float $Rate,
        //public ?int $ActualGameTypeID,
       // public ?int $ActualProductID,
    ) {}
    // public function __construct(
    //     public int $Status,
    //     public string $ProductID,
    //     public int $GameType,
    //     public ?string $TransactionID,
    //     public ?string $WagerID,
    //     public ?float $BetAmount,
    //     public ?float $TransactionAmount,
    //     public ?float $PayoutAmount,
    //     public ?float $ValidBetAmount,
    // ) {}
}