<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;
    protected $table = 'languages';

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'name_ar', 'name_en', 'name_fr', 'created_at');
    }

    // ========================== Relations ============================


    /**
     * profileSeller
     *
     * @return BelongsTo
     */
    public function profileSeller(): BelongsToMany
    {
        return $this->belongsToMany(ProfileSeller::class)
            ->withTimestamps()
            ->withPivot('level');
    }
}
