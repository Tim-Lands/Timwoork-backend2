<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Conversation;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function sendMessage(Conversation $conversation, MessageStoreRequest $request)
    {
        //   إرسال رسالة جديدة

        $user_id = Auth::user()->id;
        $conversation_id = $request->conversation_id;
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
