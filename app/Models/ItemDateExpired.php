<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDateExpired extends Model
{
    use HasFactory;
    protected $table = 'item_date_expired';

    /* --------------------------------accesor and mutators */

    /* -------------------------------- Relations ------------------------------- */

    /**
     * item
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
