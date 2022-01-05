<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * subcarts
     *
     * @return hasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
