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
            $portfolio_items = Auth::user()->profile->profile_seller->portfolio_items;
            $portfolio_items = $portfolio_items->map(function ($item) use($x_localization) {
                $tiitle_localization = "title_{$x_localization}";
                $content_localization = "content_{$x_localization}";
                $item['title'] = $item[$tiitle_localization];
                $item['content'] = $item[$content_localization];
                unset($item['title_ar']);
                unset($item['title_en']);
                unset($item['title_fr']);
                unset($item['content_ar']);
                unset($item['content_en']);
                unset($item['content_fr']);
                return $item;
            });
            return $portfolio_items;
        } catch (Exception $exc) {
            echo $exc;
        }
    }
    //


}
