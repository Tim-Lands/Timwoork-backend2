<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankTransferDetail extends Model
{
    use HasFactory;
    protected $with = ['attachments', 'country'];
    public function withdrawal()
    {
        return $this->morphOne(Withdrawal::class, 'withdrawalable');
    }


    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * attachments
     *
     * @return hasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(BankTransferDetailAttachment::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
