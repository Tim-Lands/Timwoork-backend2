<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\ConversationStoreRequest;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Conversation;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::with(['messages', 'members'])
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        return response()->success('ok', $conversations);
    }

    /**
     * show => id  دالة جلب محادثة معينة بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        //id  جلب العنصر بواسطة
        $conversation = Conversation::Selection()->whereId($id)->with(['messages' => function ($q) {
            $q->latest()->paginate(10);
        }])->first();
        // شرط اذا كان العنصر موجود
        if (!$conversation) {
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $conversation);
    }


    public function store(ConversationStoreRequest $request)
    {
        //   إضافة محادثة جديدة

        $user_id = Auth::user()->id;
        //return Auth::user()->id;
        $receiver_id = $request->receiver_id;
        try {
            DB::beginTransaction();
            $conversation = Conversation::create([
                'title' => $request->title
            ]);
            $conversation->members()->attach([$user_id, $receiver_id]);
            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'message' => $request->initial_message
            ]);

            broadcast(new MessageSent($message));
            DB::commit();
            return response()->success('لقد تمّ إضافة المحادثة بنجاح', $conversation);
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
    // send Message
    public function sendMessage(Conversation $conversation, MessageStoreRequest $request)
    {
        //   إرسال رسالة جديدة

        $user_id = Auth::user()->id;
        $conversation_id = $conversation->id;
        $message = $request->message;
        try {
            DB::beginTransaction();
            $message = Message::create([
                'user_id' => $user_id,
                'conversation_id' => $conversation_id,
                'message' => $message,
            ]);
            broadcast(new MessageSent($message));
            DB::commit();
            return response()->success('لقد تمّ إرسال الرسالة بنجاح', $message);
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
