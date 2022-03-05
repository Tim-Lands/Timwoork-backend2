<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoneyActivity extends Model
{
    use HasFactory;
    protected $casts = [
        'payload' => 'array',
    ];
    // ===========================Contants =============================
    // code
    const STATUS_BUYING         = 0;
    const STATUS_EARNING        = 1;
    const STATUS_WITHDRAW       = 2;
    const STATUS_REFUND       = 2;
    // ========================== Relations ============================
    // code

    /**
     * profile
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
