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
    const STATUS_CANCELED_BY_SITE              = 11; // ملغية من طرف الموقع بسبب نفاذ وقت المقدر
    // date_expired
    const EXPIRED_TIME_NNTIL_SOME_DAYS         = 2;
    const EXPIRED_ITEM_NULLABLE                = null;
    // item work
    const IS_ITEM_WORk = 1;
    const IS_ITEM_NOT_WORk = 0;
    // ================== Acssesor & mutators ==========================
    // code
    public function getUserIdAttribute()
    {
        return $this->profileSeller->profile->user->id;
    }
    // ============================ Scopes =============================

    /* --------------------------------accesor and mutators */

    public function getFullPathAttribute()
    {
        return url("resources_files/{$this->name}");
    }
    /* -------------------------------- Relations ------------------------------- */

    /**
     * item
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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
     * item_date_expired
     *
     * @return void
     */
    public function item_date_expired(): HasOne
    {
        return $this->hasOne(ItemDateExpired::class);
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
