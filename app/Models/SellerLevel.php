<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerLevel extends Model
{
    use HasFactory;
    protected $table = 'seller_levels';

    public const IS_BUYER = 0;
    public const IS_SELLER = 1;
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
        return $query->select('id', 'name_ar', 'name_en', 'name_fr', 'reliability', 'number_of_sales', 'products_number_max', 'number_developments_max', 'price_development_max', 'created_at');
    }

    // ========================== Relations ============================
    /**
     * profileSeller
     *
     * @return HasMany
     */
    public function profileSeller(): HasMany
    {
        return $this->hasMany(ProfileSeller::class, 'seller_level_id');
    }
}
