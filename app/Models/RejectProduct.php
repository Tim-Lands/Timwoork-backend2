<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RejectProduct extends Model
{
    use HasFactory;
    protected $table = 'reject_products';

    /* --------------------------------- Scopes --------------------------------- */

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
            'first_name',
            'last_name',
            'message_rejected',
            'email',
            'product_id',
            'created_at'
        );
    }

    /* -------------------------------- Relations ------------------------------- */

    /**
     * product
     *
     * @return BelongsTo
     */
    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
