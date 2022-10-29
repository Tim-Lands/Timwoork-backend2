<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\BanAccountEvent;
use App\Events\UnbanAccountEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\BanRequest;
use App\Models\User;
use App\Events\SendUserNotificationEvent;
use App\Http\Requests\Products\CauseRejectProductRequest;
use Carbon\Carbon;
use Cog\Laravel\Ban\Models\Ban;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class UserContoller extends Controller
{
    /**
     * get_users => جلب جميع المستخدمين
     *
     * @return void
     */
    public function get_users(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;

        // جلب جميع المستخدمين
        $users = User::selection()
            ->filter()
            ->with('profile')
            ->latest()
            ->paginate($paginate);
        // رسالة نجاح
        return response()->success(__('messages.oprations.get_all_data'), $users);
    }

    public function get_users1(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $is_banned = "all";
        if($request->has('is_banned')){
            $is_banned = $request->is_banned;
            if ($is_banned =='true' || $is_banned == true)
                return $this->get_user_banned($request);
            else if ($is_banned == "false" || $is_banned == false)
                return $this->get_user_unbanned($request);
        }
        // جلب جميع المستخدمين
        $users = User::selection()
            ->filter()
            ->with('profile')
            ->latest()
            ->paginate($paginate);
        // رسالة نجاح
        return response()->success(__('messages.oprations.get_all_data'), $users);
    }

    /**
     * sendNotification => ارسال اشعار للمستخدم
     *
     * @param  mixed $id
     * @return void
     */
    public function sendNotification(mixed $id, CauseRejectProductRequest $request)
    {
        $user = User::find($id);
        if (!$user) {
            // رسالة خطأ
            return response()->error(__('messages.errors.element_not_found'), Response::HTTP_NOT_FOUND);
        }
        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
        $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
        else {
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->cause);
            $xlocalization = $tr->getLastDetectedSource();
        }
        $tr->setSource($xlocalization);

        $cause_ar = "";
        $cause_fr = "";
        $cause_en = '';
        switch ($xlocalization) {
            case "ar":
                if (is_null($cause_en)) {
                    $tr->setTarget('en');
                    $cause_en = $tr->translate($request->cause);
                }
                if (is_null($cause_fr)) {
                    $tr->setTarget('fr');
                    $cause_fr = $tr->translate($request->cause);
                }
                $cause_ar = $request->cause;
                break;
            case 'en':
                if (is_null($cause_ar)) {
                    $tr->setTarget('ar');
                    $cause_ar = $tr->translate($request->cause);
                }
                if (is_null($cause_fr)) {
                    $tr->setTarget('fr');
                    $cause_fr = $tr->translate($request->cause);
                }
                $cause_en = $request->cause;
                break;
            case 'fr':
                if (is_null($cause_en)) {
                    $tr->setTarget('en');
                    $cause_en = $tr->translate($request->cause);
                }
                if (is_null($cause_ar)) {
                    $tr->setTarget('ar');
                    $cause_fr = $tr->translate($request->cause);
                }
                $cause_fr = $request->cause;
                break;
        }
        // جلب المستخدم من اجل ارسال الاشعار

        event(new SendUserNotificationEvent(
            $user,
            $request->cause,
            $request->cause_ar,
            $request->cause_en,
            $request->cause_fr,

        ));
        return response()->success("تم إرسال الإشعار بنجاح إلى المستخدم");
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب المستخدم الواحد
        $user = User::selection()->whereId($id)->with(['profile', 'ratings', 'favorites'])->first();
        // اذا لم يجد المستخدم
        if (!$user) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }

        // رسالة نجاح العملية
        return response()->success(__('messages.oprations.get_data'), $user);
    }

    public function show1($id)
    {
        // جلب المستخدم الواحد
        $user = User::selection()->whereId($id)->with(['profile', 'ratings', 'favorites'])->first();
        // اذا لم يجد المستخدم
        if (!$user) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }

        // رسالة نجاح العملية
        return response()->success(__('messages.oprations.get_data'), $user);
    }

    /**
     * get_user_banned => جلب الأعضاء المحظورين
     *
     * @return void
     */
    public function get_user_banned(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        // جلب المستخدمين المحظورين
        $users_banned = User::selection()->with(['profile', 'bans:bannable_id,comment,expired_at'])
            ->filter()
            ->onlyBanned()
            ->paginate($paginate);
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $users_banned);
    }

    /**
     * get_user_unbanned => جلب الأعضاء الغير محظورين
     *
     * @return void
     */
    public function get_user_unbanned(Request $request)
    {
        // تصفح المستخدمين
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        // جلب المستخدمين الغير المحظورين
        $users_unbanned = User::selection()
            ->filter()
            ->with('profile')
            ->withoutBanned()
            ->paginate($paginate);
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $users_unbanned);
    }

    /**
     * user_ban => حظر المستخدم
     *
     * @param  mixed $id
     * @return void
     */
    public function user_ban($id, Request $request)
    {
        try {
            $tr = new GoogleTranslate();
            if ($request->header('X-localization') == 'fr')
                $tr->setSource('fr');
            else if ($request->header('X-localization') == 'en')
                $tr->setSource('en');
            else
                $tr->setSource('ar');
            // جلب المستخدم
            $user = User::find($id);
            // فحص المستخدم
            if (!$user) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // حضر من قبل
            if ($user->bans) {
                // رسالة خطأ
                $user->bans()->delete();
            }

            if ($request->expired_at) {
                $data['expired_at'] = Carbon::now()->addDays($request->expired_at);
            }
            if ($request->comment) {
                $data['comment'] = $request->comment;
                $tr->setTarget('ar');
                $data['comment_ar'] = $tr->translate($request->comment);
                $tr->setTarget('en');
                $data['comment_en'] = $tr->translate($request->comment);
                $tr->setTarget('fr');
                $data['comment_fr'] = $tr->translate($request->comment);
            }

            DB::beginTransaction();
            // حظر المستخدم
            $user->ban($data);
            // عمل تسجيل خروج لكل الحسابات
            $user->tokens()->delete();
            // ارسال اشعاؤ للمستخدم
            event(new BanAccountEvent($user, $request->comment, $request->expired_at));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.user.ban_success"), $user->load('bans'));
        } catch (Exception $ex) {
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * user_unban => رفع الحظر للمستخدم
     *
     * @param  mixed $id
     * @return void
     */
    public function user_unban($id)
    {
        try {
            // جلب المستخدم
            $user = User::find($id);
            // فحص المستخدم
            if (!$user) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // تحقق من عدم وجود حظر على المستخدم
            if ($user->isNotBanned()) {
                return response()->error(__("messages.user.user_not_banned"), Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();
            // حظر المستخدم
            $user->unban();
            // ارسال اشعاؤ للمستخدم
            event(new UnbanAccountEvent($user));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.user.unban_success"), $user);
        } catch (Exception $ex) {
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }


    /**
     * expired_unban_users => تحديث حظر المستخدمين المنتهي الصلاحية
     *
     * @return void
     */
    public function expired_unban_users()
    {
        // جلب المستخدمين المحظورين
        $bans = Ban::query()
            ->with('bannable')
            ->where('expired_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->get();

        // حذف كل الحسابات المحظورة المنتهية الصلاحية
        foreach ($bans as $ban) {
            $ban->delete();
            event(new UnbanAccountEvent($ban->bannable));
        }
    }
}
