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
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();

        $conversations = $user->conversations()->with(['latestMessage', 'members' => function ($q) use ($user) {
            $q->where('user_id', '<>', $user->id)->with('profile');
        }])->withCount(['messages' => function (Builder $query) use ($user) {
            $query->where('user_id', '<>', $user->id)
                ->whereNull('read_at');
        }])
            ->orderBy('updated_at', 'desc')
            ->paginate($paginate);
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
            $q->orderBy('id', 'ASC')->with('attachments', 'user.profile');
        }])->find($id);
        // شرط اذا كان العنصر موجود
        if (!$conversation) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), 403);
        }
        $unread_messages = $conversation->messages()
            ->whereNull('read_at')
            ->where('user_id', '<>', Auth::user());
        if ($unread_messages->count() > 0) {
            foreach ($unread_messages->get() as $key => $value) {
                if ($value->user_id !== Auth::user()->id) {
                    $value->read_at = now();
                    $value->save();
                }
            }
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $conversation);
    }


    //   إضافة محادثة جديدة لخدمة
    public function product_conversation_store($id, ConversationStoreRequest $request)
    {
        $product = Product::findOrFail($id);
        $user_id = Auth::user()->id;
        $receiver_id = $request->receiver_id;

        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default

        $message_ar = null;
        $message_en = null;
        $message_fr = null;
        $title_ar = null;
        $title_en = null;
        $title_fr = null;
        $xlocalization = $request->header('X-localization');
        // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
        if (!$request->headers->has('X-localization')) {
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->initial_message);
            $xlocalization = $tr->getLastDetectedSource();
        }
        switch ($xlocalization) {
            case "ar":
                $message_en = $tr->setTarget('en')->translate($request->initial_message);
                $message_fr = $tr->setTarget('fr')->translate($request->initial_message);
                $message_ar = $request->initial_message;
                $title_en = $tr->setTarget('en')->translate($request->title);
                $title_fr = $tr->setTarget('fr')->translate($request->title);
                $title_ar = $request->title;
                break;
            case 'en':
                $message_ar = $tr->setTarget('ar')->translate($request->initial_message);
                $message_fr = $tr->setTarget('fr')->translate($request->initial_message);
                $message_en = $request->message;
                $title_ar = $tr->setTarget('ar')->translate($request->title);
                $title_fr = $tr->setTarget('fr')->translate($request->title);
                $title_en = $request->title;
                break;
            case 'fr':
                $message_en = $tr->setTarget('en')->translate($request->initial_message);
                $message_ar = $tr->setTarget('ar')->translate($request->initial_message);
                $message_fr = $request->initial_message;
                $title_ar = $tr->setTarget('ar')->translate($request->title);
                $title_fr = $tr->setTarget('fr')->translate($request->title);
                $title_en = $request->title;
                break;
            default:
        }


        try {
            DB::beginTransaction();

            $conversation = $product->conversations()->create([
                'title' => $request->title,
                'title_ar'=> $title_ar,
                'title_en'=> $title_en,
                'title_fr'=> $title_fr
            ]);
            $conversation->members()->attach([$user_id, $receiver_id]);
            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'message' => $request->initial_message,
                'message_en' => $message_en,
                'message_ar' => $message_ar,
                'message_fr' => $message_fr,
            ]);

            broadcast(new MessageSent($message));
            DB::commit();
            return response()->success(__("messages.conversation.conversation_success"), $conversation->load('messages'));
        } catch (Exception $ex) {
            //return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function item_conversation_store($id, ConversationStoreRequest $request)
    {
        //   إضافة محادثة جديدة لطلبية

        $item = Item::findOrFail($id);

        $user_id = Auth::user()->id;
        //return Auth::user()->id;
        $receiver_id = $request->receiver_id;


        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
        $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
        else {
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->initial_message);
            $xlocalization = $tr->getLastDetectedSource();
        }
        $tr->setSource($xlocalization);
        $message_ar = null;
        $message_en = null;
        $message_fr = null;
        // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
        switch ($xlocalization) {
            case "ar":
                $message_en = $tr->setTarget('en')->translate($request->initial_message);
                $message_fr = $tr->setTarget('fr')->translate($request->initial_message);
                $message_ar = $request->initial_message;
                break;
            case 'en':
                $message_ar = $tr->setTarget('ar')->translate($request->initial_message);
                $message_fr = $tr->setTarget('fr')->translate($request->initial_message);
                $message_en = $request->message;
                break;
            case 'fr':
                $message_en = $tr->setTarget('en')->translate($request->initial_message);
                $message_ar = $tr->setTarget('ar')->translate($request->initial_message);
                $message_fr = $request->initial_message;
                break;
        }



        try {
            DB::beginTransaction();
            $conversation = $item->conversation()->create([
                'title' => $request->title
            ]);
            $conversation->members()->attach([$user_id, $receiver_id]);
            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'message' => $request->initial_message,
                'message_en' => $message_en,
                'message_ar' => $message_ar,
                'message_fr' => $message_fr,
            ]);

            broadcast(new MessageSent($message));
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
        $message_ar = null;
        $message_en = null;
        $message_fr = null;
        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
        $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
        else {
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->message);
            $xlocalization = $tr->getLastDetectedSource();
        }
        $tr->setSource($xlocalization);
        // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
        switch ($xlocalization) {
            case "ar":
                $message_en = $tr->setTarget('en')->translate($message);
                $message_fr = $tr->setTarget('fr')->translate($message);
                $message_ar =  $message;
                break;
            case 'en':
                $message_ar = $tr->setTarget('ar')->translate($message);
                $message_fr = $tr->setTarget('fr')->translate($message);
                $message_en = $request->message;
                break;
            case 'fr':
                $message_en = $tr->setTarget('en')->translate($message);
                $message_ar = $tr->setTarget('ar')->translate($message);
                $message_fr =  $message;
                break;
        }


        try {
            DB::beginTransaction();
            $message = Message::create([
                'user_id' => $user_id,
                'conversation_id' => $conversation_id,
                'message' => $message,
                'type' => $request->type,
                'message_ar' => $message_ar,
                'message_en' => $message_en,
                'message_fr' => $message_fr,
                'is_reply' => $request->is_reply,
            ]);
            Conversation::where('id','=',$conversation_id)->update(['updated_at'=>DB::raw('NOW()')]);
            if ($request->has('attachments')) {
                foreach ($request->file('attachments') as $key => $value) {
                    $attachmentPath = $value;
                    $attachmentName = 'tw-attch-' . $key . $conversation_id . Auth::user()->id .  time() . '.' . $attachmentPath->getClientOriginalExtension();
                    $size = number_format($value->getSize() / 1048576, 3) . ' MB';
                    $attachmentPath->storePubliclyAs('attachments', $attachmentName, 'do');
                    //$path = Storage::putFileAs('attachments', $value, $attachmentName);
                    // تخزين معلومات المرفق
                    $message->attachments()->create([
                        'name' => $attachmentName,
                        'path' => $attachmentPath,
                        'size' => $size,
                        'mime_type' => $value->getClientOriginalExtension(),
                    ]);
                }
            }
            broadcast(new MessageSent($message));
            DB::commit();
            return response()->success(__("messages.conversation.message_success"), $message->load(['user.profile', 'attachments']));
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
