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
    const REJECTED_SELLER_OR_BUYING = 0;
    const REJECTED_BOTH_SELLER_BUYING = 1;
    /* --------------------------- Acssesor & mutators -------------------------- */
    /* --------------------------------- Scopes --------------------------------- */
    
    /* -------------------------------- Relations ------------------------------- */
    
    /**
     * Item
     *
     * @return BelongsTo
     */
    public function Item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
