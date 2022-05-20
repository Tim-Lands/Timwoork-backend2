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
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class User extends Authenticatable implements BannableContract
{
    use HasApiTokens, HasFactory, Notifiable, Billable,Bannable,FilterQueryString;

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['password_is_vide'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pm_type',
        'stripe_id',
        'pm_last_four',
        'trial_ends_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /* -------------------------------- filtering ------------------------------- */
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
        'full_name',
        'ban_tamporary',
        'ban_permanent',
    ];

    /**
     * full_name => فلترة حسب الاسم الكامل
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function full_name($query, $value)
    {
        // get the user's full name
        $query->whereHas('profile', function ($query) use ($value) {
            $query->where('full_name', 'like', '%' . $value . '%');
        });
    }

    /**
     * ban_tamporary => فلترة حسب الحظر المؤقت
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function ban_tamporary($query, $value)
    {
        $query->whereHas('bans', function ($query) use ($value) {
            $query->whereNotNull('expired_at');
        });
    }

    /**
     * ban_permanent => فلترة حسب الحظر الدائم
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function ban_permanent($query, $value)
    {
        $query->whereHas('bans', function ($query) use ($value) {
            $query->whereNull('expired_at');
        });
    }


    // ===========================Contants =============================
    // code
    // ================== Acssesor & mutators ==========================

    /**
     * getPasswordIsVideAttribute => تحقق من الباسورد فارغ ام لا
     *
     * @return void
     */
    public function getPasswordIsVideAttribute()
    {
        return $this->password == null ? true:false;
    }
    // ============================ Scopes =============================
    // code
    /**
      * scopeSelection => دالة من اجل جلب البيانات
      *
      * @param  mixed $query
      * @return object
      */
    public function scopeSelection(mixed $query): ?object
    {
        return $query->select('id', 'username', 'email', 'phone', 'status', 'created_at');
    }
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

     * verify email token
     *
     * @return HasOne
     */
    public function forgetPasswordToken(): HasOne
    {
        return $this->hasOne(ForgetPasswordToken::class);
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
}
