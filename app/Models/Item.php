<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    // ===========================Contants =============================
    // code
    const STATUS_PENDING_REQUEST       = 0;
    const STATUS_ACCEPT_REQUEST        = 1;
    const STATUS_REJECTED_REQUEST      = 2;
    const STATUS_FULFILLED_REQUEST     = 3;
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
        return $query->select('id', 'order_id', 'status', 'number_product', 'duration', 'price_product', 'profile_seller_id', 'created_at');
    }

    // ========================== Relations ============================
    // code
    /**
     * profileSeller
     *
     * @return BelongsTo
     */
    public function profileSeller(): BelongsTo
    {
        return $this->belongsTo(ProfileSeller::class, 'profile_seller_id');
    }
    /**
     * order
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * amounts
     *
     * @return HasMany
     */
    public function amounts(): HasMany
    {
        return $this->hasMany(Amount::class);
    }


    /**
     * attachments
     *
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
