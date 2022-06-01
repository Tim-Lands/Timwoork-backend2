<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends Model
{
    use HasFactory;
    protected $table = "contacts";

    /* -------------------------------- constants ------------------------------- */
    // url
    const URL_GOOGLE_DRIVE = "https://drive.google.com/";
    const URL_DROPBOX = "https://www.dropbox.com/";
    // type message

    // ============================ Scopes =============================

    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'subject', 'email', 'full_name', 'message', 'type_message', 'url', 'date_expired', 'ip_client', 'created_at');
    }

    /**
     * scopeEnquiries => دالة جلب الاستفسارات
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeEnquiries($query)
    {
        return $query->where("message_type", 1);
    }

    /**
     * scopeComplaints => دالة جلب الشكاوي
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeComplaints($query)
    {
        return $query->where("message_type", 0);
    }
}
