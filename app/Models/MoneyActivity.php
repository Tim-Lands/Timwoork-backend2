<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class MoneyActivity extends Model
{
    use HasFactory, FilterQueryString;
    protected $casts = [
        'payload' => 'array',
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
        'search'
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
        $query->whereHas('wallet', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('username', 'like', '%' . $value . '%');
                });
            });
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
        $query->whereHas('wallet', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('email', 'like', '%' . $value . '%');
                });
            });
        });
    }

    /**
     * full_name
     *
     * @param  mixed $query
     * @param  mixed $value
     * @return void
     */
    public function full_name($query, $value)
    {
        $query->whereHas('wallet', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->where('full_name', 'like', '%' . $value . '%');
            });
        });
    }

    public function search($query, $value)
    {
        $query->whereHas('wallet', function ($query) use ($value) {
            $query->whereHas('profile', function ($query) use ($value) {
                $query->where('full_name', 'like', '%' . $value . '%');
            })
            ->orWhereHas('profile', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('email', 'like', '%' . $value . '%');
                })
                ->orWhereHas('user', function ($query) use ($value) {
                    $query->where('username', 'like', '%' . $value . '%');
                });
            });
        });
    }


    // ===========================Contants =============================
    // code
    const STATUS_BUYING         = 0; // عملية شراء
    const STATUS_EARNING        = 1; // عملية ربح
    const STATUS_WITHDRAW       = 2; // عملية سحب
    const STATUS_REFUND         = 3; // عملية استعادة مال
    // ========================== Relations ============================
    // code

    /**
     * profile
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
