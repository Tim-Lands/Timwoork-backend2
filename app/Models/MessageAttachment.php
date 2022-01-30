<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;
    protected $appends = ['full_path'];


    // ============================ Scopes =============================
    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'path', 'size', 'mime_type', 'message_id', 'created_at');
    }

    public function getFullPathAttribute()
    {
        return url("attachments/{$this->name}");
    }

    // ========================== Relations ============================
    // code

    /**
     * item
     *
     * @return BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
