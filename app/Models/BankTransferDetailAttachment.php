<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransferDetailAttachment extends Model
{
    use HasFactory;

    /**
     * item
     *
     * @return BelongsTo
     */
    public function bank_transfer_detail(): BelongsTo
    {
        return $this->belongsTo(BankTransferDetail::class);
    }
}
