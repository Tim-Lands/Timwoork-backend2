<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMeProducts;
use App\Models\Product;
use App\Models\Profile;
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

    public function profile(Request $request)
    {
        //$paginate = $request->query('paginate') ?? 10;
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $profile = Profile::where(['user_id' => $user_id])->first();
        return response()->json($profile, Response::HTTP_OK);
    }

    public function badge(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $badge = Profile::where(['user_id' => $user_id])->first()->badge;
        return response()->json($badge, Response::HTTP_OK);
    }

    public function level(Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user_id =  $request->user()->id;
        $level = Profile::where(['user_id' => $user_id])->first()->level;
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
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = $request->user();

        $conversations = $user->conversations()->with(['latestMessage', 'members' => function ($q) use ($user) {
            $q->where('user_id', '<>', $user->id)->with('profile');
        }])->withCount(['messages' => function (Builder $query) use ($user) {
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
    //
    
public function products(Request $request, Response $response, $type='all'){
    try{
    if (!in_array($type, array('all','published','paused','rejected','pending','drafted')))
        return response()->error(__("messages.validation.products_type"), 400);
    $where = [
        'all'=>['is_vide'=>0],
        'published'=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED, 'is_active'=>Product::PRODUCT_ACTIVE,'status'=>Product::PRODUCT_ACTIVE],
        "paused"=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED, 'is_active'=>Product::PRODUCT_REJECT,'status'=>Product::PRODUCT_REJECT],
        "rejected"=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED,'status'=>Product::PRODUCT_ACTIVE],
        'pending'=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED],
        'drafted'=>['is_vide'=>0, 'is_draft'=>Product::PRODUCT_IS_DRAFT]
    ];
    $where_null = array('pending'=>'status','all'=>[],'published'=>[],'paused'=>[], 'rejected'=>[],'drafted'=>[]);
    $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
    $user = Auth::user();
    $products = $user->profile->profile_seller->products()
        ->where($where[$type])
        ->whereNull($where_null[$type])
        ->paginate($paginate)
        ->makeHidden([
            'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration','price','is_vide'
            ,'updated_at','created_at','deleted_at','thumbnail'
        ]);
    return response()->success(__("messages.oprations.get_all_data"), $products);
    }
    catch(Exception $exc){
        echo ($exc);
    }

}
}
