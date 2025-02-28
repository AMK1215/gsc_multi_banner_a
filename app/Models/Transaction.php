<?php

namespace App\Models;

use App\Enums\TransactionName;
use App\Models\PlaceBet;
use Bavix\Wallet\Models\Transaction as ModelsTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends ModelsTransaction
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    //protected $fillable = ['agent_id'];
    protected $fillable = [
        'payable_type',
        'payable_id',
        'wallet_id',
        'type',
        'amount',
        'confirmed',
        'meta',
        'uuid',
    ];

    protected $casts = [
        'wallet_id' => 'int',
        'confirmed' => 'bool',
        'meta' => 'json',
        'name' => TransactionName::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wager()
    {
        return $this->belongsTo(Wager::class);
    }

    // public function placeBets()
    // {
    //     return $this->hasMany(PlaceBet::class, 'transaction_id', 'id');
    // }

    public function targetUser()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Yangon');
            $model->updated_at = Carbon::now('Asia/Yangon');
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Yangon');
        });
    }
}
