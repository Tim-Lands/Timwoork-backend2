<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

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
        return $query->select('id', 'title',  'created_at');
    }
    // ========================== Relations ============================
    // code

    public function conversationable()
    {
        return $this->morphTo();
    }
    /**
     * messages
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('id', 'ASC');
    }

    /**
     * latest messages
     *
     */
    public function latest_msg(): HasMany
    {
        return $this->messages()->order_by('id', 'desc')->first();
    }



    /**
     * members
     *
     * @return HasMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_users');
    }
}
