<?php

namespace App\Http\Controllers\Me;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationStoreRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ConversationController extends Controller
{
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
