<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\DeleteMessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{


    /**
     * get_all_notifications => جلب كل الاشعارات
     *
     * @param  mixed $request
     * @return void
     */
    public function get_all_notifications(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;

        $notifications = DB::table('notifications')
            ->join('users', 'users.id', '=', 'notifications.notifiable_id')
            ->select('notifications.*', 'users.id as user_id', 'users.email', 'users.username')
            ->paginate($paginate);
        // جلب جميع الاشعارات
        /*$notifications = User::selection()->with(['notifications' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->paginate($paginate)->map(function ($notification) {
            return $notification->notifications;
        })->flatten()*/;

        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $notifications);
    }


    /**
     * get_all_conversations => جلب جميع المحادثات
     *
     * @return void
     */
    public function get_all_conversations(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        // جلب جميع المحادثات الموقع الحالي
        $conversations = Conversation::selection()->with('members')->latest()->paginate($paginate);
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $conversations);
    }

    /**
     * get_conversation => جلب المحادثة الواحدة
     *
     * @param  mixed $id
     * @return void
     */
    public function get_conversation($id)
    {
        // جلب المحادثة الواحدة
        $conversation = Message::selection()
                ->where('conversation_id', $id)
                ->with('user', function ($query) {
                    $query->select('id', 'username', 'email');
                })->get();
        // get conversation members
        $conversation_members = Conversation::selection()
            ->where('id', $id)
            ->with('members', function ($query) {
                $query->select('username', 'email', 'user_id');
            })
            ->first()
            ->members;
        $data = [
            'conversation' => $conversation,
            'members' => $conversation_members
        ];
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_data'), $data);
    }

    /**
     * delete_conversation => حذف المحادثة الواحدة
     *
     * @param  mixed $id
     * @return void
     */
    public function delete_conversation($id)
    {
        try {
            // جلب المحادثة الواحدة
            $conversation = Conversation::find($id);
            // شرط للمحادثة
            if (!$conversation) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            DB::beginTransaction();
            // حذف المحادثة
            $conversation->delete();
            DB::commit();
            // اظهار العناصر
            return response()->success(__('messages.oprations.delete_success'), $conversation);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * update_message => تعديل الرسالة
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update_message(Request $request, $id)
    {
        try {
            // جلب المحادثة الواحدة
            $message = Message::find($id);
            // اذا لم يجد المحادثة
            if (!$message) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            DB::beginTransaction();
            // تحديث الرسالة
            $message->update(['message' => $request->message]);
            DB::commit();
            // اظهار العناصر
            return response()->success(__('messages.oprations.update_success'), $message);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete_message => حذف الرسالة
     *
     * @param  mixed $id
     * @return void
     */
    public function delete_message($id, Request $request)
    {
        try {
            // جلب المحادثة الواحدة
            $message = Message::find($id);
            // اذا لم يجد المحادثة
            if (!$message) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // get the user for this message
            $user = $message->user;

            DB::beginTransaction();
            // حذف الرسالة
            $message->delete();
            // ارسال اشعاؤ للمستخدم
            event(new DeleteMessageEvent($user, $request->cause));
            DB::commit();
            // اظهار العناصر
            return response()->success(__('messages.oprations.delete_success'), $message);
        } catch (Exception $ex) {
            return $ex;
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
