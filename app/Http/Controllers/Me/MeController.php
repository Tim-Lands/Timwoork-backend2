<?php

namespace App\Http\Controllers\Me;

use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetMeProducts;
use App\Models\PortfolioItems;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    public function index(Request $request)
    {
        //$paginate = $request->query('paginate') ?? 10;
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user =  $request->user();
        $email_verified = True;
        if (is_null($user->email_verified_at))
            $email_verified = False;
        $user->email_verified = $email_verified;
        unset($user->email_verified_at);
        return response()->json($user, Response::HTTP_OK);

        /* load([
            'profile.profile_seller.badge',
            'profile.profile_seller.level',
            'profile.profile_seller.skills',
            'profile.badge',
            'profile.level',
            'profile.country',
            'profile.currency',
            'profile.wallet' => function ($q) {
                return $q->with('activities');
            },
        ]); */

        // make some columns hidden in response
        /* $notifications_count = $user->unreadNotifications->count();
        $msg_count = $this->getUnreadMessagesCount($user);
        $cart_items_count = $this->getCartItemsCount($user);
        $data = [
            'user_details' => $user,
            'unread_messages_count' => $msg_count,
            'unread_notifications_count' => $notifications_count,
            'cart_items_count' => $cart_items_count
        ];
        return response()->json($data, Response::HTTP_OK); */
    }

    public function followers(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }

        $followers = Auth::user()->load([
            'profile' => function ($q) {
                $q->select('id', 'user_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
            },
            'profile.followers' => function ($q) {
                $q->select('follower_id', 'following_id', 'first_name', 'last_name', 'full_name', 'avatar', 'avatar_url', 'is_seller', 'level_id', 'badge_id')
                    ->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
            },
            'profile.followers.level' => function ($q) use ($x_localization) {
                $q->select('id', "name_{$x_localization} AS name");
            },
            'profile.followers.badge' => function ($q) use ($x_localization) {
                $q->select('id', "name_{$x_localization} AS name");
            }
        ]);
        return $followers;
    }

    public function followings(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }

            $followings = Auth::user()->load([
                'profile' => function ($q) {
                    $q->select('id', 'user_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                },
                'profile.followings' => function ($q) {
                    $q->select('follower_id', 'following_id', 'first_name', 'last_name', 'full_name', 'avatar', 'avatar_url', 'is_seller', 'level_id', 'badge_id')
                        ->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                },
                'profile.followings.level' => function ($q) use ($x_localization) {
                    $q->select('id', "name_{$x_localization} AS name");
                },
                'profile.followings.badge' => function ($q) use ($x_localization) {
                    $q->select('id', "name_{$x_localization} AS name");
                }
            ]);
            return $followings;
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function favourites(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $user = Auth::user();
            $id = $user->id;
            $favourites = $user->load([
                'profile'=>function($q) {
                    $q->select('id', 'user_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                },
                'profile.favourites'=>function($q) use($x_localization, $id){
                    $q->select('favourites.id','seller_id', 'profile_id', "content_{$x_localization} AS content", "title_{$x_localization} AS title", 'cover_url', 'url', 'completed_date')
                    ->withCount(['likers AS likers_count', 'fans AS fans_count'])
                    ->withExists([
                        'likers AS is_liked' => function ($q) use ($id) {
                            $q->where('profile_id', $id);
                        },
                        'fans AS is_favourite' => function ($q) use ($id) {
                            $q->where('profile_id', $id);
                        }
                    ]);
                    
                },
                'profile.favourites.seller'=>function($q) use($x_localization){
                    $q->select('id', 'profile_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                },
                'profile.favourites.seller.profile'=>function($q) use($x_localization){
                    $q->select('id', 'first_name', 'last_name', 'avatar', 'avatar_url', 'user_id', 'level_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                },
                'profile.favourites.seller.profile.level'=>function($q) use($x_localization){
                    $q->select('id', "name_{$x_localization}");
                },
                'profile.favourites.seller.profile.user'=>function($q){
                    $q->select('id', 'username');
                }
            ])
            ->profile
            ->favourites;
            return $favourites;

        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function currency(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            return Auth::user()->profile->currency;
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function profile(Request $request)
    {
        //$paginate = $request->query('paginate') ?? 10;
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $profile = Profile::where(['user_id' => $user_id])
            ->with(
                [
                    'country' => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name");
                    }, 'badge' => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name");
                    },
                    'level' => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name", 'value_bayer_min', 'value_bayer_max', 'type', 'number_developments', 'price_developments', 'number_sales');
                    },
                ]
            )->first();
        return response()->json($profile, Response::HTTP_OK);
    }

    public function badge(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $badge = Profile::with(['badge' => function ($query) use ($x_localization) {
            $query->select('id', "name_{$x_localization} AS name");
        }])->where(['user_id' => $user_id])->get()->first()->badge;
        return response()->json($badge, Response::HTTP_OK);
    }

    public function level(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $level = Profile::with(['level' => function ($query) use ($x_localization) {
            $query->select("id", "name_{$x_localization} AS name", "value_bayer_min", "value_bayer_max");
        }])->where(['user_id' => $user_id])->first()->level;
        return response()->json($level, Response::HTTP_OK);
    }

    public function notifications(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;

        /*         $notifications = Auth::user()->notifications()->orderBy('created_at')
            ->paginate($paginate)->groupBy(function ($data) {
                return $data->created_at->diffForHumans();
            }); */
        $user = $request->user();
        $notifications = $user->notifications()->paginate($paginate);
        return response()->json(
            $notifications,
            Response::HTTP_OK
        );
    }

    public function unread_notifications_count(Request $request)
    {
        try {
            $user = $request->user();
            $unread_notifications_count = $user->unreadnotifications->count();
            return response($unread_notifications_count);
        } catch (Exception $ex) {
            echo $ex;
        }
    }

    public function conversations(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
            $user = Auth::user();

            $conversations = $user->conversations()->with(['latestMessage', 'members' => function ($q) use ($user, $x_localization) {
                $q->where('user_id', '<>', $user->id)
                    #->without(['profile.bank_d'])
                    ->with([
                        'profile' => function ($q) use ($x_localization) {
                            $q->select('id', 'first_name', 'last_name', 'avatar', 'avatar_url', 'gender', 'is_seller', 'user_id', 'country_id', 'badge_id', 'level_id', 'full_name');
                        },
                        'profile.level' => function ($q) use ($x_localization) {
                            $q->select('id', "name_{$x_localization} AS name");
                        },
                        'profile.badge' => function ($q) use ($x_localization) {
                            $q->select('id', "name_{$x_localization} AS name");
                        },
                        'profile.country' => function ($q) use ($x_localization) {
                            $q->select('id', 'flag', 'code_phone', "name_{$x_localization} AS name ");
                        },
                        'profile.paypal_account' => function ($q) use ($x_localization) {
                            $q->select('created_at');
                        },
                        'profile.bank_account' => function ($q) use ($x_localization) {
                            $q->select('created_at');
                        },
                        'profile.bank_transfer_detail' => function ($q) use ($x_localization) {
                            $q->select('created_at');
                        },
                    ]);
            }])->withCount(['messages' => function (Builder $query) use ($user) {
                $query->where('user_id', '<>', $user->id)
                    ->whereNull('read_at');
            }])
                ->orderBy('updated_at', 'desc')
                ->paginate($paginate);
            return response()->success('ok', $conversations);
        } catch (Exception $exc) {
            echo ($exc);
        }
    }

    public function unread_conversations_count(Request $request)
    {
        try {
            $user = $request->user();
            $unread_messages = $user->conversations->loadCount(['messages' => function ($q) use ($user) {
                $q->whereNull('read_at')
                    ->where('user_id', '<>', $user->id);
            }])->where('messages_count', '>', '0')->sortByDesc('updated_at');
            return response(['data' => array_values($unread_messages->toArray()), 'count' => $unread_messages->count()]);
        } catch (Exception $ex) {
            echo $ex;
        }
    }
    public function status(User $user, Request $request)
    {

        $status = strtolower($request->status) == "true";
        $user->status = $status;
        $user->save();
        if ($status) {
            broadcast(new UserOnline($user));
        } else {
            broadcast(new UserOffline($user));
        }
    }

    public function portfolio(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $portfolio = Auth::user()->load([
                'profile'=>function($q){
                    $q->select('id', 'user_id');
                },
                'profile.profile_seller'=>function($q){
                    $q->select('id', 'profile_id');
                },
                'profile.profile_seller.portfolio_items'=>function ($q) use($x_localization){
                    $q->select('id', 'seller_id', "title_{$x_localization} AS title", "content_{$x_localization} AS content", 'url', 'cover_url', 'completed_date')
                    ->withCount(['viewers AS views']);
                }
            ])->profile->profile_seller->portfolio_items;
            return $portfolio;
        } catch (Exception $exc) {
            echo $exc;
        }
    }
    //


}
