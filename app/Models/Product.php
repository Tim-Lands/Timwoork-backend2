<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    // ===========================Contants =============================
    // code
    const PRDUCT_REJECT = 0;
    const PRDUCT_ACTIVE = 1;
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
        return $query->select('id', 'title', 'slug', 'content', 'price', 'duration', 'category_id', 'profile_seller_id', 'buyer_instruct', 'status', 'created_at');
    }

    // ========================== Relations ============================


    /**
     * category
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * develpments
     *
     * @return HasMany
     */
    public function develpments(): HasMany
    {
        return $this->hasMany(Develpment::class, 'product_id');
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
}
