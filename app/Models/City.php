<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code


    // ========================== Relations ============================

    /**
     * country
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
