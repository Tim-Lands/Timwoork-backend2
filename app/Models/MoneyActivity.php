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
    const STATUS_BUYING         = 0; // عملية شراء
    const STATUS_EARNING        = 1; // عملية ربح
    const STATUS_WITHDRAW       = 2; // عملية سحب
    const STATUS_REFUND         = 3; // عملية استعادة مال
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
