<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory;
    protected $table = 'profiles';

    //protected $with = ['profile_seller'];

    // ===========================Constants =============================
    public const COMPLETED_SETP_ONE = 1;
    public const COMPLETED_SETP_TWO = 2;
    public const COMPLETED_SETP_THREE = 3;
    // code
    // ================== Acssesor & mutators ==========================
    // code


    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================

    /**
     * user
     *
     * @return HasOne
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * level
     *
     * @return BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * country
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    /**
     * badge
     *
     * @return BelongsTo
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, "badge_id");
    }

    /**
     * profile_selller
     *
     * @return HasOne
     */
    public function profile_seller(): HasOne
    {
        return $this->hasOne(ProfileSeller::class, 'profile_id');
    }

    /**
     * cards
     *
     * @return HasMany
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * wallet
     *
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
}
