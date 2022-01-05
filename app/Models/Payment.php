<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $casts = [
        'payload' => 'array',
    ];
    // ===========================Contants =============================
    // code
    const STATUS_PENDING     = 0;
    const STATUS_PAID        = 1;
    const STATUS_FAILED      = 2;


    /**
     * Get the order that owns the Payment
     *
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
