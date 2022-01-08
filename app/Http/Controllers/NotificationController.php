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


    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at')->paginate(2)->groupBy(function ($data) {
            /*         $da = Carbon::parse($data->created_at)->locale('ar');
            return  $da->isoFormat('Do MMMM', 'MMMM YYYY');; */
            return $data->created_at->diffForHumans();
        });
        return response()->success('لقد تم جلب الاشعارات بنجاح', $notifications);
    }
}
