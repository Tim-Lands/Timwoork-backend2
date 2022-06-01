<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    use HasFactory;
    protected $table = 'levels';

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
        return $query->select('id', 'name_ar', 'name_en', 'name_fr', 'type', 'number_developments', 'price_developments', 'number_sales', 'value_bayer', 'created_at');
    }

    // ========================== Relations ============================

    /**
     * profile
     *
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'level_id');
    }


    /**
     * profileSeller
     *
     * @return HasOne
     */
    public function profileSeller(): HasOne
    {
        return $this->hasOne(ProfileSeller::class, 'level_id');
    }
}
