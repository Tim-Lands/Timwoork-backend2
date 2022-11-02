<?php

namespace App\Http\Controllers\Me;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationStoreRequest;
use App\Models\Conversation;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ConversationController extends Controller
{
    public function show(Request $request, $id){
        
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $conversation = Conversation::Selection()->whereId($id)->with(['messages' => function ($q) use($x_localization) {
            $q->select('id','user_id', 'conversation_id', "message_{$x_localization} AS message",'read_at', 'created_at', 'type', 'is_reply')->orderBy('id', 'ASC')->with('attachments', 'user.profile');
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
    public function create_conversation($id, ConversationStoreRequest $request)
    {
        $product = Product::findOrFail($id);
        $user_id = Auth::user()->id;
        $receiver_id = $request->receiver_id;

        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default

        $message_ar = null;
        $message_en = null;
        $message_fr = null;
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
            default:
        }


        try {
            DB::beginTransaction();

            $conversation = $product->conversations()->create([
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
            return response()->success(__("messages.conversation.conversation_success"), $conversation->load('messages'));
        } catch (Exception $ex) {
            //return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
