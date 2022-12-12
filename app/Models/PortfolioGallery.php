<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioGallery extends Model
{
    use HasFactory;
    protected $table = 'portfolio_item_gallery';

    /**
     * item
     *
     * @return BelongsTo
     */
    public function portfolio_item(): BelongsTo
    {
        return $this->belongsTo(PortfolioItems::class);
    }
}
