<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePayment extends Model
{
    use HasFactory;
    protected $table = 'type_payments';

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
        return $query->select('id', 'name_ar', 'name_en', 'precent_of_payment', 'value_of_cent', 'created_at');
    }

    /**
     * scopePaymentActive => البوابات النشطة
     *
     * @param  mixed $q
     * @return void
     */
    public function scopePaymentActive($q)
    {
        return $q->whereStatus(1);
    }

    /**
     * scopePaymentDisactive => البوابات الغير النشطة
     *
     * @param  mixed $q
     * @return void
     */
    public function scopePaymentDisactive($q)
    {
        return $q->whereStatus(0);
    }
    // ========================== Relations ============================
    // code
}
