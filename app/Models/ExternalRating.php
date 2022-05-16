<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// خاص بالتقييمات الخارجية للخدمات 
class ExternalRating extends Model
{
    use HasFactory;

    /************* Constants ***************************/
    const PENDING            = 0;
    const COMPLETED          = 1;
    const CANCELED           = 2;


    /****************  RelationShips  *************************** */
    /**
     * Get the product that owns the ExternalRating
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
