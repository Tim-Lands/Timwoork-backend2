<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;
    protected $with = ['withdrawalable.profile'];

    // ===========================Contants =============================
    const PENDING_WITHDRAWAL            = 0;
    const COMPLETED_WITHDRAWAL          = 1;

    const TYPE_PAYPAL                   = 0;
    const TYPE_WISE                     = 1;
    const TYPE_BANK                     = 2;
    const TYPE_BANK_TRANSFER            = 3;


    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================


    /***************************** */
    /**Relations */
    public function withdrawalable()
    {
        return $this->morphTo();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
