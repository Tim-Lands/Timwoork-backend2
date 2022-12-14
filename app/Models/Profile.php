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
    protected $appends = ['avatar_path'];
    protected $with = ['level', 'badge', 'wise_account', 'paypal_account', 'bank_account.country', 'bank_transfer_detail.country'];

    // ===========================Constants =============================
    public const COMPLETED_SETP_ONE = 1;
    public const COMPLETED_SETP_TWO = 2;
    public const COMPLETED_SETP_THREE = 3;
    // code
    // ================== Acssesor & mutators ==========================
    // code
    public function getAvatarPathAttribute()
    {
        return 'https://timwoork-space.ams3.digitaloceanspaces.com/avatars/' . $this->avatar;
    }

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
    public function currency(): BelongsTo
    {
        return $this->BelongsTo(Currency::class, 'currency_id', 'id');
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

    /**
     * wise_account
     *
     * @return HasOne
     */
    public function wise_account(): HasOne
    {
        return $this->hasOne(WiseAccount::class);
    }

    /**
     * paypal_account
     *
     * @return HasOne
     */
    public function paypal_account(): HasOne
    {
        return $this->hasOne(PaypalAccount::class);
    }

    /**
     * bank_account
     *
     * @return HasOne
     */
    public function bank_account(): HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    /**
     * bank_transfer_detail
     *
     * @return HasOne
     */
    public function bank_transfer_detail(): HasOne
    {
        return $this->hasOne(BankTransferDetail::class);
    }


    /**
     * item
     *
     * @return BelongsToMany
     */
    public function liked_portfolios(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioItems::class, 'likes', 'profile_id','portfolio_item_id')->withTimestamps();
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioItems::class, 'favourites', 'profile_id', 'portfolio_item_id')->withTimestamps();
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function viewed_portfolios(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioItems::class, 'portfolio_views', 'profile_id');
    }

    // users that are followed by this user
    public function following()
    {
        return $this->belongsToMany($this::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    // users that follow this user
    public function followers()
    {
        return $this->belongsToMany($this::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }
}
