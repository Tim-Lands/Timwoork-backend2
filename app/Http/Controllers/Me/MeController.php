<?php

namespace App\Http\Controllers\Me;

use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetMeProducts;
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
        if(is_null($user->email_verified_at))
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

    public function currency(Request $request){
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        return Auth::user()->profile->currency;
        }
        catch(Exception $exc){
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
        ['country'=>function($q) use($x_localization){
            $q->select('id',"name_{$x_localization} AS name");
        }
        ,'badge'=>function($q) use($x_localization){
            $q->select('id',"name_{$x_localization} AS name");
        },
        'level'=>function($q) use($x_localization){
            $q->select('id',"name_{$x_localization} AS name", 'value_bayer_min', 'value_bayer_max','type', 'number_developments', 'price_developments', 'number_sales');
        },
        ])->first();
        return response()->json($profile, Response::HTTP_OK);
    }

    public function badge(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $badge = Profile::with(['badge'=>function($query) use($x_localization){
            $query->select('id',"name_{$x_localization} AS name");
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
        $level = Profile::with(['level'=>function($query) use($x_localization){
            $query->select("id","name_{$x_localization} AS name","value_bayer_min","value_bayer_max");
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
        $unread_notifications = $user->unreadNotifications;
        return response()->json([
            'notifications' => $notifications,
            'unread_notifications' => $unread_notifications,
        ], Response::HTTP_OK);
    }

    public function conversations(Request $request)
    {
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = $request->user();

        /* $conversations = $user->with(['conversations'=>function($query) use($x_localization){
            $query->select("title");
        }])->first()->conversations()->with(['latestMessage', 'members' => function ($q) use ($user) {
            $q->where('user_id', '<>', $user->id)->with('profile');
        }])->withCount(['messages' => function (Builder $query) use ($user) {
            $query->where('user_id', '<>', $user->id)
                ->whereNull('read_at');
        }])
            ->orderBy('updated_at', 'desc')
            ->paginate($paginate); */
            $conversations = $user->with(
                ["conversations:id,title_{$x_localization} AS title,created_at,updated_at,conversationable_type,conversationable_id",
                "conversations.latestMessage",
                'conversations.members:id,id,username,email,phone,code_phone'
                ])
            ->withCount(['messages' => function (Builder $query) use ($user) {
                $query->where('user_id', '<>', $user->id)
                    ->whereNull('read_at');
            }])
                ->orderBy('updated_at', 'desc')
                ->paginate($paginate);
        $unread_messages = $user->conversations->loadCount(['messages' => function ($q) use ($user) {
            $q->whereNull('read_at')
                ->where('user_id', '<>', $user->id);
        }]);
        return response()->success('ok', ['conversations'=>$conversations,"unread_conversations"=>$unread_messages]);
    }
    catch(Exception $exc){
        echo($exc);
    }
}
public function status(User $user, Request $request){
    
    $status = strtolower($request->status)=="true";
    $user->status = $status;
        $user->save();
        if($status){
            broadcast(new UserOnline($user));
        }
            else{
            broadcast(new UserOffline($user));
        }
    }
    //
    

}
