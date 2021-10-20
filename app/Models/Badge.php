<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Badge extends Model
{
    use HasFactory;
    protected $table = 'badges';

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
        return $this->hasOne(Profile::class, 'badge_id');
    }

    /**
     * profileSeller
     *
     * @return HasOne
     */
    public function profileSeller(): HasOne
    {
        return $this->hasOne(ProfileSeller::class, 'badge_id');
    }
}
