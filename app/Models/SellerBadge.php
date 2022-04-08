<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerBadge extends Model
{
    use HasFactory;
    protected $table = 'seller_badges';

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
        return $query->select('id', 'name_ar', 'name_en', 'name_fr', 'precent_deducation', 'created_at');
    }
    // ========================== Relations ============================

    /**
     * profileSeller
     *
     * @return HasMany
     */
    public function profileSeller(): HasMany
    {
        return $this->hasMany(ProfileSeller::class, 'seller_badge_id');
    }
}
