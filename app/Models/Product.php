<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Product extends Model
{
    use HasFactory, FilterQueryString, SoftDeletes;

    /**
     * table
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['full_path_thumbnail', 'ratings_avg_rating'];


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
        'subcategories',
        'category',
        'count_buying',
        'seller_level',
        'ratings_avg',
        'badge',
        "user_status",
        'status',
        'is_active',
        'most_selling',
        'most_recent'
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
     * tags => الوسوم
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

    // badges => الباقات
    public function badge($query, $value)
    {
        return $query->whereHas('seller_level', function ($q) use ($value) {
            $q->whereIn('id', $value);
        });
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

    /**
     * most_selling
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function most_selling($query, $value)
    {
        return $query->orderBy('count_buying', 'desc');
    }

    /**
     * most_recent => اخر المنتجات
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function most_recent($query, $value)
    {
        return $query->orderBy('created_at', 'desc');
    }


    /**
     * ratings_avg => التقييم المتوسط
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return Object
     */
    public function ratings_avg($query, $value)
    {
        $ratings = explode(',', $value);
        if ($value == 1) {
            return $query->whereIn('ratings_avg', [0, 1])
                ->orderBy('ratings_avg', 'desc');
        }
        return $query->whereIn('ratings_avg', $ratings)
            ->orderBy('ratings_avg', 'desc');
    }


    /**
     * user_status => الحالة
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function user_status($query, $value)
    {
        return $query->whereHas('profileSeller', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('status', $value);
                });
            });
        });
    }

    // filter by level of seller
    public function seller_level($query, $value)
    {
        return $query->whereHas('profileSeller', function ($query) use ($value) {
            $query->where('seller_level_id', $value);
        });
    }

    /**
     * status => الحالة الخدمة
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function status($query, $value)
    {
        if ($value == 2) {
            return $query->whereNull('status');
        }
        return $query->where('status', $value);
    }

    /**
     * is_active
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function is_active($query, $value)
    {
        return $query->where('is_active', $value);
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
    // حالة الخدمة اذا كانت في قائمة المسودات
    const PRODUCT_IS_DRAFT = 1;
    const PRODUCT_IS_NOT_DRAFT = 0;
    /* --------------------------- Accessor & Metators -------------------------- */
    // code


    /**
     * getRatingsAvgRatingAttribute
     *
     * @return void
     */
    public function getRatingsAvgRatingAttribute()
    {
        return $this->ratings->count() != 0 ? round(array_sum(array_map(function ($key) {
            return $key['rating'];
        }, $this->ratings->toArray())) / $this->ratings->count()) : 0;
    }

    /**
     * getUrlVideoAttribute => جلب رابط الفيديو
     *
     * @return void
     */
    public function getVideoUrlAttribute()
    {
        return $this->video->url_video;
    }

    /**
     * getFullPathThumbnail => جلب رابط الصورة الامامية
     *
     * @return void
     */
    public function getFullPathThumbnailAttribute()
    {
        return "https://timwoork-space.ams3.digitaloceanspaces.com/products/thumbnails/" . $this->thumbnail;
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
            '*'
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
    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'product_id');
    }

    /**
     * video
     *
     * @return HasOne
     */
    public function video(): HasOne
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
        return $this->belongsToMany(Tag::class)->withPivot('value', 'label');
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

    /**
     * conversations
     */
    public function conversations()
    {
        return $this->morphMany(Conversation::class, 'conversationable');
    }

    /**
     * reject_product
     *
     * @return HasOne
     */
    public function reject_product(): HasOne
    {
        return $this->hasOne(RejectProduct::class, 'product_id');
    }

    /**
     * external_rating
     *
     * @return HasOne
     */
    public function external_rating(): HasOne
    {
        return $this->hasOne(ExternalRating::class);
    }

    /* -------------------------------------------------------------------------- */
}
