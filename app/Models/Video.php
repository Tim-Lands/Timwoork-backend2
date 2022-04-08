<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $table = "videos";

    /* --------------------------- Acssesor & mutators -------------------------- */
    /* --------------------------------- Scopes --------------------------------- */

    /* -------------------------------- Relations ------------------------------- */
    
    /**
     * Item
     *
     * @return BelongsTo
     */
    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
