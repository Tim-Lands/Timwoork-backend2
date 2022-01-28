<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\ConversationStoreRequest;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Message;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            return response()->error(__("messages.errors.element_not_found"), 403);
        }
        $unread_messages = $conversation->messages->whereNot('user_id', Auth::user())->get();
        foreach ($unread_messages as $key => $value) {
            $value->seen_at = time();
            $value->save();
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $conversation);
    }


    //   إضافة محادثة جديدة
    public function product_conversation_store($id, ConversationStoreRequest $request)
    {
        $product = Product::findOrFail($id);
        $user_id = Auth::user()->id;
        //return Auth::user()->id;
        $receiver_id = $request->receiver_id;
        try {
            DB::beginTransaction();

            $conversation = $product->conversations()->create([
                'title' => $request->title
            ]);
            $conversation->members()->attach([$user_id, $receiver_id]);
            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'message' => $request->initial_message
            ]);

            //broadcast(new MessageSent($message));
            DB::commit();
            return response()->success(__("messages.conversation.conversation_success"), $conversation->load('messages'));
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function item_conversation_store($id, ConversationStoreRequest $request)
    {
        //   إضافة محادثة جديدة

        $item = Item::findOrFail($id);

        $user_id = Auth::user()->id;
        //return Auth::user()->id;
        $receiver_id = $request->receiver_id;
        try {
            DB::beginTransaction();
            $conversation = $item->conversations()->create([
                'title' => $request->title
            ]);
            $conversation->members()->attach([$user_id, $receiver_id]);
            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'message' => $request->initial_message
            ]);

            //broadcast(new MessageSent($message));
            DB::commit();
            return response()->success(__("messages.conversation.conversation_success"), $conversation);
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
                'type' => $request->type,
                'is_reply' => $request->is_reply,
            ]);

            if ($request->has('attachments')) {
                foreach ($request->file('attachments') as $key => $value) {
                    $attachmentPath = $value;
                    $attachmentName = 'tw-attch-' . $conversation_id . Auth::user()->id .  time() . '.' . $attachmentPath->getClientOriginalExtension();
                    $size = number_format($value->getSize() / 1048576, 3) . ' MB';
                    $path = Storage::putFileAs('attachments', $value, $attachmentName);
                    // تخزين معلومات المرفق
                    $message->attachments()->create([
                        'name' => $attachmentName,
                        'full_path' => $path,
                        'size' => $size,
                        'type_file' => 0,
                        'mime_type' => $attachmentPath->getClientMimeType(),
                    ]);
                }
            }
            //broadcast(new MessageSent($message));
            DB::commit();
            return response()->success(__("messages.conversation.message_success"), $message);
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
