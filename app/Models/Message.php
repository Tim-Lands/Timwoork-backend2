<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    // ===========================Contants =============================
    // code
    const MESSAGE_TYPE_TEXT                 = 0;
    const MESSAGE_TYPE_INSTRUCTION          = 1;
    const MESSAGE_TYPE_IMAGE                = 2;
    const MESSAGE_TYPE_AUDIO                = 3;
    const MESSAGE_TYPE_VEDIO                = 4;
    const MESSAGE_TYPE_FILE                 = 5;
    const MESSAGE_TYPE_ZIP                  = 6;

    // ================== Acssesor & mutators ==========================
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
        return $query->select('id', 'user_id', 'conversation_id', 'message', 'read_at',  'created_at');
    }
    // ========================== Relations ============================
    // code


    /**
     * conversation
     *
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * user
     *
     * @return hasMany
     */
    public function attachments(): hasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }
}
