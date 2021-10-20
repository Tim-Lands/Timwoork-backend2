<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================

    /**
     * cities
     *
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'county_id');
    }


    /**
     * profile
     *
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'country_id');
    }
}
