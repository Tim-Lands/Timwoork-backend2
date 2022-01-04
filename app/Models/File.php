<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;
    protected $table = "files";

    /* --------------------------- Acssesor & mutators -------------------------- */
    /* --------------------------------- Scopes --------------------------------- */

    /* -------------------------------- Relations ------------------------------- */
    
    /**
     * Item
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
