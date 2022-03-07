<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankTransferDetail extends Model
{
    use HasFactory;

    public function withdrawal()
    {
        return $this->morphOne(Withdrawal::class, 'withdrawalable');
    }


    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
