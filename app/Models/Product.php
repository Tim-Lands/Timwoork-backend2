<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Product extends Model
{
    use HasFactory, FilterQueryString;
    protected $table = 'products';
    protected $filters = [
        'sort',
        'greater',
        'greater_or_equal',
        'less',
        'less_or_equal',
        'between',
        'not_between',
        'like'
    ];
    // ===========================Contants =============================
    // code
    // حالة الخدمة مرفوضة
    const PRODUCT_REJECT = 0;
    // حالة الخدمة نشطة
    const PRODUCT_ACTIVE = 1;
    // مراحل انشاء الخدمة
    const PRODUCT_STEP_ONE    = 1;
    const PRODUCT_STEP_TWO    = 2;
    const PRODUCT_STEP_THREE  = 3;
    const PRODUCT_STEP_FOUR   = 4;
    const PRODUCT_STEP_FIVE   = 5;
    // اكتمال عملية انشاء الخدمة
    const PRODUCT_IS_COMPLETED = 1;
    // ================== Accessor & Metators ==========================
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
        return $query->select('id', 'title', 'slug', 'content', 'price', 'duration', 'category_id', 'profile_seller_id', 'thumbnail', 'buyer_instruct', 'status', 'created_at');
    }

    /**
     * scopeProductActive عملية تصفية الخدمات المنشطة
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeProductActive($query): ?object
    {
        return $query->whereStatus(Product::PRODUCT_ACTIVE);
    }

    /**
     * scopeProductReject => عملية تصفية الخدمات المرفوضة
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeProductReject($query): ?object
    {
        return $query->whereStatus(Product::PRODUCT_REJECT);
    }

    // ========================== Relations ============================


    /**
     * category
     *
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * develpments
     *
     * @return HasMany
     */
    public function developments(): HasMany
    {
        return $this->hasMany(Development::class, 'product_id');
    }

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
     * galaries
     *
     * @return HasMany
     */
    public function galaries(): HasMany
    {
        return $this->hasMany(Galary::class, 'product_id');
    }

    /**
     * product_tag
     *
     * @return BelongsToMany
     */

    public function product_tag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * shortener
     *
     * @return HasOne
     */
    public function shortener(): HasOne
    {
        return $this->hasOne(Shortener::class, "product_id");
    }


    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
