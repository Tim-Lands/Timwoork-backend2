<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;
    protected $with = ['country', 'profile'];
    public function withdrawal()
    {
        return $this->morphOne(Withdrawal::class, 'withdrawalable');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(WiseCountry::class, 'wise_country_id', 'id');
    }
}
