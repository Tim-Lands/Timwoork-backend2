<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    // ===========================Contants =============================
    // code
    // حالة التقييم مرفوض
    const RATING_REJECT = 0;
    // حالة التقييم مرفوض
    const RATING_ACTIVE = 1;

    // ================== Accessor & Metators ==========================
    // code
    // ============================ Scopes =============================


    // ========================== Relations ============================

    /**
     * Get ratings
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get ratings
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
