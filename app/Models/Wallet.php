<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

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
        return $query->select('id', 'profile_id', 'amouts_total', 'amount_pending',  'created_at');
    }

    // ========================== Relations ============================
    // code


    /**
     * amounts
     *
     * @return HasMany
     */
    public function amounts(): HasMany
    {
        return $this->hasMany(Amount::class);
    }


    /**
     * attachments
     *
     * @return HasMany
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(profile::class);
    }
}
