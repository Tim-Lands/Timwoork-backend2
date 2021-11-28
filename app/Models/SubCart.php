<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubCart extends Model
{
    use HasFactory;

    protected $table = 'sub_carts';
    // protected $hidden = ['pivot', 'id'];
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
        return $query->select('id', 'price_product', 'product_id', 'quantity');
    }
    // ========================== Relations ============================
    // code


    /**
     * cart
     *
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
     * cart_development
     *
     * @return BelongsToMany
     */
    public function subcart_developments(): BelongsToMany
    {
        return $this->belongsToMany(Development::class);
    }
}
