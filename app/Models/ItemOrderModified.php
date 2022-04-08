<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOrderModified extends Model
{
    use HasFactory;
    protected $table = "item_order_modifieds";


    /* -------------------------------- Contants -------------------------------- */
    const PENDING      = 0;
    const ACCEPTED     = 1;
    const REJECTED     = 2;
    /* --------------------------- Acssesor & mutators -------------------------- */
    /* --------------------------------- Scopes --------------------------------- */

    /* -------------------------------- Relations ------------------------------- */

    /**
     * Item
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
