<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// خاص بالتقييمات الخارجية للبروفايلات 
class ExternalAccountRating extends Model
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
    public function profileSeller(): BelongsTo
    {
        return $this->belongsTo(ProfileSeller::class);
    }
}
