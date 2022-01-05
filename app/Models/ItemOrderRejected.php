<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOrderRejected extends Model
{
    use HasFactory;
    protected $table = "item_order_rejecteds";


    /* -------------------------------- Contants -------------------------------- */
    const REJECTED_BY_BUYER    = 1;
    const REJECTED_BY_SELLER   = 1;
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
