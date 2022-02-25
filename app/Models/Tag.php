<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $appends = ['value', 'label'];
    protected $table = 'tags';
    protected $hidden = ['pivot'];

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code

    public function getValueAttribute()
    {
        return $this->name;
    }

    public function getLabelAttribute()
    {
        return $this->name;
    }
    // ============================ Scopes =============================

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'name', 'created_at');
    }
    // ========================== Relations ============================
    // code
}
