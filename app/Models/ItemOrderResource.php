<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOrderResource extends Model
{
    use HasFactory;
    protected $table = "item_order_resources";

    /* -------------------------------- Contants -------------------------------- */
    const RESOURSE_PENDING_ACCEPTED = 0;
    const RESOURSE_ACCEPTED = 1;
    const RESOURCE_MODIFICATION = 2;
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
