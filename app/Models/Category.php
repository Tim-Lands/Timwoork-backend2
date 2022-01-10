<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    // ===========================Contants =============================
    // عدد التصنيفات التي يتم اظهارها في الهيدر
    const SUBCATEGORY_DISPLAY = 12;
    // عدد الصفحات التي سيظهرهم
    const PAGINATE = 5;


    // ================== Acssesor & mutators ==========================

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
        return $query->select('id', 'name_ar', 'name_en', 'name_fr', 'slug', 'description_ar', 'description_en', 'description_fr', 'icon', 'parent_id', 'created_at');
    }

    /**
     * scopeParent => دالة تعمل على تصفية التصنيفات الرئيسية
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeParent(mixed $query): ?object
    {
        return $query->whereNull('parent_id');
    }


    /**
     * scopeChild => دالة تعمل على تصفية التصنيفات الفرعية
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeChild(mixed $query): ?object
    {
        return $query->whereNotNull('parent_id');
    }

    // ========================== Relations ============================

    /**
     * subCategories
     *
     * @return HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * products
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Category
     *
     * @return BelongsTo
     */
    public function Category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')->Parent();
    }
}
