<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileSeller extends Model
{
    use HasFactory;
    protected $table = 'profile_sellers';

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================
    // code

    /**
     * profile_seller_skill
     *
     * @return BelongsToMany
     */
    public function profile_seller_skill(): BelongsToMany
    {
        return $this->belongsToMany(ProfileSeller::class, 'profile_seller_skill', 'product_id', 'tag_id');
    }


    /**
     * badge
     *
     * @return BelongsTo
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id');
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
     * profile
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * languages
     *
     * @return HasMany
     */
    public function languages(): HasMany
    {
        return $this->hasMany(Language::class, 'profile_seller_id');
    }

    /**
     * profissions
     *
     * @return BelongsToMany
     */
    public function profissions(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'profissions');
    }
}
