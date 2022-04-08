<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Galary extends Model
{
    use HasFactory;
    /**
     * table
     *
     * @var string
     */
    protected $table = 'galaries';
    protected $appends = ['data_url'];

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code

    public function getDataUrlAttribute()
    {
        return 'https://timwoork-space.ams3.digitaloceanspaces.com/products/galaries-images/' . $this->path;
    }
    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================

    /**
     * product
     *
     * @return BelongsTo
     */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
