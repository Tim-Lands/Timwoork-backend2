<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CartItem extends Model
{
    use HasFactory;

    /**
     * table
     *
     * @var string
     */
    protected $table = 'cart_items';

    /**
     * appends
     *
     * @var array
     */

    protected $appends = ['duration_product'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['product'];

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        "price_product"    => 'decimal:2',
    ];
    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================


    /**
     * getDurationProductAttribute => جلب مدة الخدمة
     *
     * @return void
     */
    public function getDurationProductAttribute()
    {
        return $this->product->duration;
    }

    // ============================ Scopes =============================

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'cart_id', 'price_product', 'product_id', 'quantity');
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
    public function cartItem_developments(): BelongsToMany
    {
        return $this->belongsToMany(Development::class, 'cartitem_development', 'cart_item_id', 'development_id');
    }
}
