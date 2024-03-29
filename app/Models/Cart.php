<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory;

    /**
     * table
     *
     * @var string
     */
    protected $table = 'carts';

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        "total_price"    => 'decimal:2',
        "price_with_tax" => 'decimal:2',
        "tax"            => 'decimal:2',
    ];
    /* -------------------------------- Contants -------------------------------- */
    // code
    const IS_BUYING = 1;
    const IS_NOT_BUYING = 0;
    // Type of payment
    const PAYPAL = 'Paypal';
    const STRIPE = 'Stripe';
    /* --------------------------- Acssesor & mutators -------------------------- */
    // code
    /* --------------------------------- Scopes --------------------------------- */
    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'user_id', 'is_buying', 'total_price', 'price_with_tax', 'tax');
    }
    /*
    * scopeSelection => دالة من اجل جلب البيانات
    *
    * @param  mixed $query
    * @return object
    */
    public function scopeActiveCart(mixed $query): ?object
    {
        return $query->where('is_buying', 0);
    }

    /**
     * scopeIsNotBuying => السلة غير مباعة
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeIsNotBuying(mixed $query)
    {
        return $query->where('is_buying', self::IS_NOT_BUYING);
    }

    /**
     * scopeIsNotBuying => السلة مباعة
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeIsBuying(mixed $query)
    {
        return $query->where('is_buying', self::IS_BUYING);
    }

    /**
     * scopePaypal => جلب مبيعات بواسطة باي بال
     *
     * @return void
     */
    public function scopePaypal()
    {
        return $this->cart_payments->where('name_en', 'Paypal')->first()->pivot;
    }

    /**
     * scopeStripe => جلب مبيعات بواسطة ستريب
     *
     * @return void
     */
    public function scopeStripe()
    {
        return $this->cart_payments->where('name_en', 'Stripe')->first()->pivot;
    }

    /* -------------------------------- Relations ------------------------------- */
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
     * order
     *
     * @return BelongsTo
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }


    /**
     * subcarts
     *
     * @return hasMany
     */
    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * cart_payments
     *
     * @return BelongsToMany
     */
    public function cart_payments(): BelongsToMany
    {
        return $this->belongsToMany(TypePayment::class, 'cart_payments', 'cart_id', 'type_payment_id')
            ->using(CartPayment::class)
            ->withPivot(['tax', 'total', 'total_with_tax']);
    }

    /**
     * payments
     *
     * @return hasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
