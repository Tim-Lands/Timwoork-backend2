<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Product extends Model
{
    use HasFactory, FilterQueryString;

    /**
     * table
     *
     * @var string
     */
    protected $table = 'products';
    /**
     * filters
     *
     * @var array
     */
    protected $filters = [
        'sort',
        'greater',
        'greater_or_equal',
        'less',
        'less_or_equal',
        'between',
        'not_between',
        'like',
        'seller_name',
        'tags',
        'popular',
        'price',
        'title',
        'subCat',
        'category',
        'count_buying'
    ];

    
    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2'
    ];

    /* -------------------------------- functions ------------------------------- */
    /**
     * seller_name
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function seller_name($query, $value)
    {
        return $query->whereHas('profileSeller', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->where(DB::raw(
                    // REPLACE will remove the double white space with single (As defined)
                    "REPLACE(
                        /* CONCAT will concat the columns with defined separator */
                        CONCAT(
                            /* COALESCE operator will handle NUll values as defined value. */
                            COALESCE(first_name,''),' ',
                            COALESCE(last_name,'')
                        ),
                    '  ',' ')"
                ), 'like', '%' . $value . '%');
            });
        });
    }

    /**
     * tags
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function tags($query, $value)
    {
        $tag_ids = explode(',', $value);
        return $query->whereHas('product_tag', function ($q) use ($tag_ids) {
            $q->whereIn('tag_id', $tag_ids);
        });
    }

    /**
     * category
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
     * subCat
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function subCat($query, $value)
    {
        $cat_ids = explode(',', $value);
        return $query->whereHas('subcategory', function ($query) use ($cat_ids) {
            $query->whereIn('id', $cat_ids);
        });
    }

    /**
     * popular
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function popular($query, $value)
    {
        return $query->orderBy('ratings_avg', 'desc')
            ->orderBy('ratings_count', 'desc');
    }
    /* -------------------------------- Constants ------------------------------- */
    // code
    //حالة الخدمة مرفوضة او معطلة
    const PRODUCT_REJECT = 0;
    // حالة الخدمة نشطة
    const PRODUCT_ACTIVE = 1;
    // مراحل انشاء الخدمة
    const PRODUCT_STEP_ONE    = 1;
    const PRODUCT_STEP_TWO    = 2;
    const PRODUCT_STEP_THREE  = 3;
    const PRODUCT_STEP_FOUR   = 4;
    const PRODUCT_STEP_FIVE   = 5;
    // اكتمال عملية انشاء الخدمة
    const PRODUCT_IS_COMPLETED = 1;
    /* --------------------------- Accessor & Metators -------------------------- */
    // code
        
    /**
     * getUrlVideoAttribute => جلب رابط الفيديو
     *
     * @return void
     */
    public function getVideoUrlAttribute()
    {
        return $this->video->url_video;
    }

    /* --------------------------------- Scopes --------------------------------- */

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select(
            'id',
            'title',
            'slug',
            'content',
            'price',
            'duration',
            'category_id',
            'profile_seller_id',
            'count_buying',
            'thumbnail',
            'buyer_instruct',
            'status',
            'created_at'
        );
    }

    /**
     * scopeProductActive عملية تصفية الخدمات المنشطة
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeProductActive($query): ?object
    {
        return $query->whereStatus(Product::PRODUCT_ACTIVE);
    }

    /**
     * scopeProductReject => عملية تصفية الخدمات المرفوضة
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeProductReject($query): ?object
    {
        return $query->whereStatus(Product::PRODUCT_REJECT);
    }

    /* -------------------------------- Relations ------------------------------- */

    /**
     * category
     *
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * develpments
     *
     * @return HasMany
     */
    public function developments(): HasMany
    {
        return $this->hasMany(Development::class, 'product_id');
    }

    /**
     * profileSeller
     *
     * @return BelongsTo
     */
    public function profileSeller(): BelongsTo
    {
        return $this->belongsTo(ProfileSeller::class, 'profile_seller_id');
    }


    /**
     * galaries
     *
     * @return HasMany
     */
    public function galaries(): HasMany
    {
        return $this->hasMany(Galary::class, 'product_id');
    }
    
        
    /**
     * file
     *
     * @return HasOne
     */
    public function file():HasOne
    {
        return $this->hasOne(File::class, 'product_id');
    }

    /**
     * video
     *
     * @return HasOne
     */
    public function video():HasOne
    {
        return $this->hasOne(Video::class, 'product_id');
    }

    /**
     * product_tag
     *
     * @return BelongsToMany
     */

    public function product_tag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * shortener
     *
     * @return HasOne
     */
    public function shortener(): HasOne
    {
        return $this->hasOne(Shortener::class, "product_id");
    }


    /**
     * cart_items
     *
     * @return HasMany
     */
    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * ratings
     *
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /* -------------------------------------------------------------------------- */
}
