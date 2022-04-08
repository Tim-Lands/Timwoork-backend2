<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * عرض جميع الاشعارات الخاصة بالمستخدم
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;

        /*         $notifications = Auth::user()->notifications()->orderBy('created_at')
            ->paginate($paginate)->groupBy(function ($data) {
                return $data->created_at->diffForHumans();
            }); */
        $notifications = Auth::user()->notifications()->paginate($paginate);
        return response()->success('لقد تم جلب الاشعارات بنجاح', $notifications);
    }

    /**
     *  عرض الاشعار الواحد
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        return response()->success('لقد تم جلب الاشعارات بنجاح', $notification);
    }

    /**
     *  تحديد كل الاشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->success('لقد تم تحديد جميع الاشعارات كمقروءة');
    }
    /**
     *  تحديد  الاشعار الواحد كمقروء
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        $notification->markAsRead();
        return response()->success('لقد تم تحديد الاشعار كمقروء');
    }
}
