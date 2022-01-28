<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;
    protected $appends = ['user_id'];
    protected $table = 'items';
    // ===========================Contants =============================
    // code
    const STATUS_PENDING                       = 0; // قيد الانتظار
    const STATUS_CANCELLED_BY_BUYER            = 1; // ملغية من طرف المشتري
    const STATUS_REJECTED_BY_SELLER            = 2; // مرفوضة من البائع قبل التنفيذ
    const STATUS_ACCEPT                        = 3; // قيد التنفيذ
    const STATUS_CANCELLED_REQUEST_BUYER       = 4; // طلب الغاء من طرف المشتري
    const STATUS_CANCELLED_BY_SELLER           = 5; // ملغية من البائع بعد التنفيذ
    const STATUS_DILEVERED                     = 6; // قيد الاستلام
    const STATUS_FINISHED                      = 7; // مكتملة
    const STATUS_SUSPEND                       = 8; // معلقة
    const STATUS_MODIFIED_REQUEST_BUYER        = 9; // طلب تعديل من طرف المشتري
    const STATUS_SUSPEND_CAUSE_MODIFIED        = 10; //  معلقة بسبب طلب التعديل

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
        return $this->hasMany(ItemAttachment::class);
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

    /**
     * item_modified
     *
     * @return void
     */
    public function item_modified(): HasOne
    {
        return $this->hasOne(ItemOrderModified::class);
    }

    /**
     * conversations
     */
    public function conversation()
    {
        return $this->morphOne(Conversation::class, 'conversationable');
    }
}
