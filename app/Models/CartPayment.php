<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CartPayment extends Pivot
{
    use HasFactory;

    // ===========================Contants =============================

    // ================== Acssesor & mutators ==========================

    protected $casts = [
        "total"          => 'decimal:2',
        "total_with_tax" => 'decimal:2',
        "tax"            => 'decimal:2',
    ];


    // ============================ Scopes =============================
}
