<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be appends.
     */
    protected $appends = ['unread_messages_count'];
    // protected $with = ['profile'];
    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================
    public function getUnreadMessagesCountAttribute()
    {
        $count = $this->conversations->loadCount(['messages' => function ($q) {
            $q->whereNull('read_at')
                ->where('user_id', '!=', Auth::id());
        }]);
        if (!empty($count)) {
            return $count[0]->messages_count;
        } else {
            return 0;
        }
    }

    // code
    // ============================ Scopes =============================
    // code
    // ========================== Relations ============================

    /**

     * verify email token 
     *
     * @return HasOne
     */
    public function verifyEmailCode(): HasOne
    {
        return $this->hasOne(VerifyEmailCode::class);
    }

    /**
     * providers
     *
     * @return HasMany
     */
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class);
    }


    /**
    
     * profile
     *
     * @return HasOne
     */
    public function profile(): HasOne
    {

        return $this->hasOne(Profile::class, 'user_id');
    }

    /**
     * favorites
     *
     * @return BelongsToMany
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }

    /**
     * ratings
     *
     * @return BelongsToMany
     */
    public function ratings(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ratings');
    }

    /**
     * carts
     *
     * @return HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * messages
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * conversations
     *
     * @return HasMany
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users');
    }

    /**
     * Unread message count
     * @return int
     */
}
