<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioItems extends Model
{
    use HasFactory;
    protected $table = 'portfolio_items';
    
    /**
     * item
     *
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(ProfileSeller::class);
    }

    /**
     * item
     *
     * @return HasMany
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(PortfolioGallery::class,'portfolio_item_id');
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function fans(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'favorites');
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class,'portfolio_item_tags','portfolio_item_id')->withPivot('value', 'label');
    }

}
