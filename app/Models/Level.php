<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    use HasFactory;
    protected $table = 'levels';

    const IS_BUYER = 0;
    const IS_SELLER = 1;
    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code
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
