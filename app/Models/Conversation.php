<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Conversation extends Model
{
    use HasFactory,FilterQueryString;

    protected $table = 'conversations';

    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    // code
    // ============================ filtering =============================
    /**
    * filters
    *
    * @var array
    */
    protected $filters = [
        'sort',
        'greater',
        'greater_or_equal',
        'less',
        'less_or_equal',
        'between',
        'not_between',
        'like',
        'username',
        'email',
        'full_name',
    ];

    /**
     * username
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function username($query, $value)
    {
        $query->whereHas('members', function ($query) use ($value) {
            $query->where('username', 'like', '%' . $value . '%');
        });
    }

    /**
     * email
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function email($query, $value)
    {
        $query->whereHas('members', function ($query) use ($value) {
            $query->where('email', 'like', '%' . $value . '%');
        });
    }


    public function full_name($query, $value)
    {
        // filter by full name from profile

        $query->whereHas('messages', function ($query) use ($value) {
            $query->whereHas('user', function ($query) use ($value) {
                // filter by full name from profile
                $query->whereHas('profile', function ($query) use ($value) {
                    // filter by full name from profile
                    $query->where('full_name', 'like', '%' . $value . '%');
                });
            });
            // filter by full name from profile
        });
    }

    // ============================ scopes =============================
    /**
     * scopeSelection => دالة من اجل جلب البيانات
     *
     * @param  mixed $query
     * @return object
     */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'title', 'created_at');
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
     * latest message
     *
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * latest message
     *
     */
    public function receiver()
    {
        if (Auth::check()) {
            return $this->members()->where('user_id', '<>', Auth::id())->first();
        }
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
