<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProfileSeller extends Model
{
    use HasFactory;
    protected $table = 'profile_sellers';

    protected $with = ['level', 'badge'];

    // ===========================Constants =============================
    public const COMPLETED_SETP_ONE = 1;
    public const COMPLETED_SETP_TWO = 2;
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================
    // code



    /**
     * badge
     *
     * @return BelongsTo
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(SellerBadge::class, 'seller_badge_id');
    }

    /**
     * level
     *
     * @return BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(SellerLevel::class, 'seller_level_id');
    }

    /**
     * profile
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * products
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * languages
     *
     * @return HasMany
     */
    public function languages(): belongsToMany
    {
        return $this->belongsToMany(Language::class)
            ->withTimestamps()
            ->withPivot('level');
    }

    /**
     * profissions
     *
     * @return BelongsToMany
     */
    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'professions');
    }

    /**
     * profile_seller_skills
     *
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withPivot('level');
    }

    /**
     * item
     *
     * @return BelongsTo
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }


    /**
     * item
     *
     * @return HasOne
     */
    public function external_rating(): HasOne
    {
        return $this->hasOne(ExternalAccountRating::class);
    }

    /**
     * item
     *
     * @return HasMany
     */
    public function portfolio_items(): HasMany
    {
        return $this->hasMany(PortfolioItems::class);
    }


    
}
