<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    // ===========================Contants =============================
    // code


    // ================== Acssesor & mutators ==========================
    // code


    // ============================ Scopes =============================
    // code


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
}
