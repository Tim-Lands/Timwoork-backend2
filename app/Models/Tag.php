<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $hidden = ['pivot'];

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code

    // ============================ Scopes =============================

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'name', 'label', 'value', 'created_at');
    }
    // ========================== Relations ============================
    // code
}
