<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $with = ['cart_development'];

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
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
        return $query->select('id', 'user_id', 'product_id', 'quantity',  'created_at');
    }
    // ========================== Relations ============================
    // code

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * product
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * order
     *
     * @return BelongsTo
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    /**
     * cart_development
     *
     * @return BelongsToMany
     */
    public function cart_development(): BelongsToMany
    {
        return $this->belongsToMany(Development::class);
    }
}
