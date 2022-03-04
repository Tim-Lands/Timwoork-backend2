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
    // تقيم معلق
    const RATING_SUSPEND = 0;
    // حالة التقييم مرفوض
    const RATING_REJECT = 2;
    // حالة التقييم مقبول
    const RATING_ACTIVE = 1;

    // ================== Accessor & Metators ==========================
    // code
    // ============================ Scopes =============================

    /**
        * scopeSelection => دالة من اجل جلب البيانات
        *
        * @param  mixed $query
        * @return object
        */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select(
            'id',
            'comment',
            'product_id',
            'reply',
            'status',
            'rating',
            'created_at'
        );
    }
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
