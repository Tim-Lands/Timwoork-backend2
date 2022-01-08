<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;
    protected $appends = ['user_id'];
    protected $table = 'items';
    // ===========================Contants =============================
    // code
    const STATUS_PENDING_REQUEST       = 0;
    const STATUS_ACCEPT_REQUEST        = 1;
    const STATUS_REJECTED_BY_SELLER    = 2;
    const STATUS_REJECTED_BY_BUYER     = 3;
    const STATUS_REJECTED_REQUEST      = 4;
    const STATUS_FINISHED              = 5;
    // ================== Acssesor & mutators ==========================
    // code
    public function getUserIdAttribute()
    {
        return $this->profileSeller->profile->user->id;
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
     * amount
     *
     * @return HasMany
     */
    public function amount(): HasOne
    {
        return $this->hasOne(Amount::class);
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

    /**
     * Resource
     *
     * @return HasOne
     */
    public function Resource(): HasOne
    {
        return $this->hasOne(ItemOrderResource::class);
    }

    /**
     * item_rejected
     *
     * @return void
     */
    public function item_rejected(): HasOne
    {
        return $this->hasOne(ItemOrderRejected::class);
    }
}
