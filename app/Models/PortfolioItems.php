<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class PortfolioItems extends Model
{
    use HasFactory, FilterQueryString;
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
        return $this->hasMany(PortfolioGallery::class, 'portfolio_item_id');
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function fans(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'favourites', 'portfolio_item_id');
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'views', 'portfolio_item_id');
    }


    /**
     * item
     *
     * @return BelongsToMany
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'likes', 'portfolio_item_id');
    }

    /**
     * item
     *
     * @return BelongsToMany
     */
    public function tags($query, $value)
    {
        $tag_ids = explode(',', $value);

        return $query->whereHas('portfolio_item_tags', function ($q) use ($tag_ids) {
            $q->whereIn('tag_id', $tag_ids);
        });
    }

    public function portfolio_item_tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'portfolio_item_tags', 'portfolio_item_id');
    }

    /**
     * category => الاقسام الرئيسية
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function category($query, $value)
    {
        $cat_ids = explode(',', $value);
        return $query->whereHas('subcategory', function ($query) use ($cat_ids) {
            $query->whereIn('parent_id', $cat_ids);
        });
    }
    
    /**
     * subcategories
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function subcategories($query, $value)
    {
        $subcat_ids = explode(',', $value);
        return $query->whereHas('subcategory', function ($query) use ($subcat_ids) {
            $query->whereIn('id', $subcat_ids);
        });
    }


    /**
     * category
     *
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
