<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\Me\Product\ActiveProduct;
use App\Http\Requests\Products\ProductStepOneRequest;
use App\Http\Requests\Products\ProductStepThreeRequest;
use App\Http\Requests\Products\ProductStepTwoRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ProductController extends Controller
{
    //
    public function index(Request $request, Response $response){
        try{
            $type='all';
            if($request->filled('type'))
                $type = $request->input('type');
            else 
                $type = 'all';

        if (!in_array($type, array('all','published','paused','rejected','pending','drafted')))
            return response()->error(__("messages.validation.products_type"), 400);
        $where = [
            'all'=>['is_vide'=>0],
            'published'=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED, 'is_active'=>Product::PRODUCT_ACTIVE,'status'=>Product::PRODUCT_ACTIVE],
            "paused"=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED, 'is_active'=>Product::PRODUCT_REJECT,'status'=>Product::PRODUCT_REJECT],
            "rejected"=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED,'status'=>Product::PRODUCT_ACTIVE],
            'pending'=>['is_vide'=>0, 'is_completed'=>Product::PRODUCT_IS_COMPLETED],
            'drafted'=>['is_vide'=>0, 'is_draft'=>Product::PRODUCT_IS_DRAFT]
        ];
        $where_null = array('pending'=>'status','all'=>[],'published'=>[],'paused'=>[], 'rejected'=>[],'drafted'=>[]);
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()
            ->where($where[$type])
            ->whereNull($where_null[$type])
            ->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration','price','is_vide'
                ,'updated_at','created_at','deleted_at','thumbnail'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
        }
        catch(Exception $exc){
            echo ($exc);
        }
    
    }

    public function show($id, Request $request){
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $product = Product::whereId($id)
                            ->select('id','profile_seller_id',"title_{$x_localization} AS title", "slug", "content_{$x_localization} AS content", "price", 'duration', 'count_buying',"thumbnail","buyer_instruct_{$x_localization} AS buyer_instruct", "status", "is_active","current_step","is_completed","is_draft","category_id", "created_at", "updated_at", "ratings_avg","ratings_count")
                            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                            ->with(['developments','product_tag','galaries','file','video','shortener'])
                            ->first();
        // شرط اذا لم يتم ايجاد الخدمة
        if (!$product) {
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $product);
    }
    catch(Exception $exc){
        echo $exc;
    }
    }

    public function store()
    {
        try {
            if (!Auth::user()->profile->is_seller) {
                return response()->error(__("messages.product.you_are_not_seller"), 422);
            }
            //جلب عدد خدمات
            $count_products_seller =  Auth::user()->profile->profile_seller->products->where('is_vide', 0)->count();
            // جلب عدد المطلبوب من انشاء الخدمة من المستوى
            $number_of_products_seller = Auth::user()->profile->profile_seller->level->products_number_max;
            // شرط اضافة خدمة
            if ($count_products_seller > $number_of_products_seller) {
                return response()->error(__("messages.product.number_of_products_seller"), 422);
            }

            // ============= انشاء المعرف للخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء معرف جديد للخدمة
            $product = Product::create([
                'profile_seller_id' => Auth::user()->profile->profile_seller->id,
                'is_draft'          => Product::PRODUCT_IS_DRAFT,
            ]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // اظهار العناصر
            return response()->success(__("messages.oprations.get_all_data"), Product::selection()->where('id', $product->id)->first());
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function storeStepOne($id, ProductStepOneRequest $request)
    {
        try {
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            $tr->setSource(); // Translate from English
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            else {
                $tr->setSource();
                $tr->setTarget('en');
                $tr->translate($request->title);
                $xlocalization = $tr->getLastDetectedSource();
            }
            $tr->setSource($xlocalization);
            $title_ar = $request->title_ar;
            $title_en = $request->title_en;
            $title_fr = $request->title_fr;
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 422);
            }
            // جلب التصنيف الفرعي
            $subcategory = Category::child()->where('id', $request->subcategory)->exists();
            // التحقق اذا كان موجود ام لا
            if (!$subcategory) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            switch ($xlocalization) {
                case "ar":
                    if (is_null($title_en)) {
                        $tr->setTarget('en');
                        $title_en = $tr->translate($request->title);
                    }
                    if (is_null($title_fr)) {
                        $tr->setTarget('fr');
                        $title_fr = $tr->translate($request->title);
                    }
                    $title_ar = $request->title;
                    break;
                case 'en':
                    if (is_null($title_ar)) {
                        $tr->setTarget('ar');
                        $title_ar = $tr->translate($request->title);
                    }
                    if (is_null($title_fr)) {
                        $tr->setTarget('fr');
                        $title_fr = $tr->translate($request->title);
                    }
                    $title_en = $request->title;
                    break;
                case 'fr':
                    if (is_null($title_en)) {
                        $tr->setTarget('en');
                        $title_en = $tr->translate($request->title);
                    }
                    if (is_null($title_ar)) {
                        $tr->setTarget('ar');
                        $title_ar = $tr->translate($request->title);
                    }
                    $title_fr = $request->title;
                    break;
            }
            $data = [
                'title'             => $request->title,
                'title_ar'          => $title_ar,
                'title_en'          => $title_en,
                'title_fr'          => $title_fr,
                'slug'              => $product->id . '-' . slug_with_arabic($request->title),
                'category_id'       =>  (int)$request->subcategory,
                'is_vide'           => 0,
            ];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_ONE) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_ONE;
            }

            // جلب الوسوم من المستخدم
            $tag_request_values = array_values(array_map(function ($key) {
                return strtolower($key["value"]);
            }, $request->tags));
            // حلب الوسوم الموجودة داخل القواعد البيانات
            $tags = Tag::select('id', 'name')->whereIn('name', $tag_request_values)->get();

            // جلب الاسماء الوسوم مع فلترة تكرارها
            $get_name_tags = array_unique(array_map(function ($key) {
                return $key["name"];
            }, $tags->toArray()));

            // جلب المعرفات الملفترة و وضعهم في مصفوفة
            $ids = array_values(array_map(function ($key) {
                return $key['id'];
            }, array_filter($tags->toArray(), function ($key) {
                return strtolower($key["name"]) == $key["name"];
            })));
            // جلب الاسماء الجديدة الغير موجودة في قواعد البيانات
            $new_tags = array_values(array_diff($tag_request_values, $get_name_tags));
            /* --------------------- انشاء المرحلة الاولى في الخدمة --------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الاولى
            $product->update($data);
            // اضافة الكلمات المفتاحية الكلمات المفتاحية او الوسوم
            // شرط اذا كانت هناك كلمات مفتاحية جديدة
            if (!empty($new_tags)) {
                // عمل لوب من اجل اضافة كلمة جيدة
                foreach ($new_tags as $tag) {
                    // اضافة وسم جديد
                    $tag = Tag::create([
                        'name' => $tag,
                        'label' => $tag,
                        'value' => $tag
                    ]);
                    // وضع معرف الوسم في المصفوفة
                    $ids[] = $tag->id;
                }
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->sync($ids);
            } else {
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->sync($ids);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_one"), $product);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * storeStepTwo => دالة انشاء المرحلة الثانية من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepTwoRequest $request
     * @return object
     */
    public function storeStepTwo(mixed $id, ProductStepTwoRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // جلب عدد المطلبوب من انشاء التطويرات من المستوى
            $number_developments_max = Auth::user()->profile->profile_seller->level->number_developments_max;
            // جلب عدد المطلبوب من السعر التطويرات من المستوى
            $price_development_max = Auth::user()->profile->profile_seller->level->price_development_max;
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثانية
            $data = [
                'price'           => (float)$request->price,
                'duration'        => (int)$request->duration
            ];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_TWO) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_TWO;
            }
            // انشاء مصفوفة جديدة من اجل عملية اضافة تطويرات
            (object)$developments = [];

            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default

            // شرط اذا كانت هناك توجد تطورات
            if ($request->only('developments') != null) {
                if (count($request->developments) > $number_developments_max) {
                    return response()->error(__("messages.product.number_developments_max"), 422);
                }
                // جلب المرسلات من العميل و وضعهم فالمصفوفة الجديدة

                $xlocalization = "ar";
                if ($request->headers->has('X-localization'))
                    $xlocalization = $request->header('X-localization');
                else {
                    $tr->setSource();
                    $tr->setTarget('en');
                    $tr->translate($request->developments[0]->title);
                    $xlocalization = $tr->getLastDetectedSource();
                }
                $tr->setSource($xlocalization);

                foreach ($request->only('developments')['developments'] as $key => $value) {
                    $value['title_ar'] = $request->title_ar ? $request->title_ar : null;
                    $value['title_en'] = $request->title_ar ? $request->title_en : null;
                    $value['title_fr'] = $request->title_ar ? $request->title_fr : null;
                    // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى



                    switch ($xlocalization) {
                        case "ar":
                            if (is_null($value['title_ar'])) {
                                $tr->setTarget('en');
                                $value['title_en'] = $tr->translate($value['title']);
                            }
                            if (is_null($value['title_fr'])) {
                                $tr->setTarget('fr');
                                $value['title_fr'] = $tr->translate($value['title']);
                            }
                            $value['title_ar'] = $value['title'];
                            break;
                        case 'en':
                            if (is_null($value['title_ar'])) {
                                $tr->setTarget('ar');
                                $value['title_ar'] = $tr->translate($value['title']);
                            }
                            if (is_null($value['title_fr'])) {
                                $tr->setTarget('fr');
                                $value['title_fr'] = $tr->translate($value['title']);
                            }
                            $value['title_en'] = $value['title'];
                            break;
                        case 'fr':
                            if (is_null($value['title_en'])) {
                                $tr->setTarget('en');
                                $value['title_en'] = $tr->translate($value['title']);
                            }
                            if (is_null($value['title_ar'])) {
                                $tr->setTarget('ar');
                                $value['title_ar'] = $tr->translate($value['title']);
                            }
                            $value['title_fr'] = $value['title'];
                            break;
                    }
                    //$value['title_ar'] = $value['title'];
                    $developments[] = $value;
                    // اذا كان السعر اكبر
                    if ($value['price'] > $price_development_max) {
                        return response()->error(__("messages.product.price_development_max"), 422);
                    }
                }
            }
            // =============== انشاء المرحلة الثانية في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثانية
            $product->update($data);
            // شرط اذا كانت هناط تطويرات من قبل
            if ($product->developments) {
                // حدف كل التطويرات
                $product->developments()->forceDelete();
            }

            // اضافة تطويرات جديدة
            $product->developments()->createMany($developments);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_two"), $product->load('developments'));
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * storeStepThree => دالة انشاء المرحلة الثالثة من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepThreeRequest $request
     * @return JsonResponse
     */
    public function storeStepThree(mixed $id, ProductStepThreeRequest $request)
    {
        try {
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            else {
                $tr->setSource();
                $tr->setTarget('en');
                $tr->translate($request->title);
                $xlocalization = $tr->getLastDetectedSource();
            }
            $tr->setSource($xlocalization);
            $buyer_ar = $request->buyer_ar;
            $buyer_en = $request->buyer_en;
            $buyer_fr = $request->buyer_fr;
            $content_ar = $request->content_ar;
            $content_en = $request->content_en;
            $content_fr = $request->content_fr;

            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();     // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            switch ($xlocalization) {
                case "ar":
                    //////////buyer
                    if (is_null($buyer_en)) {
                        $tr->setTarget('en');
                        $buyer_en = $tr->translate($request->buyer_instruct);
                    }
                    if (is_null($buyer_fr)) {
                        $tr->setTarget('fr');
                        $buyer_fr = $tr->translate($request->buyer_instruct);
                    }
                    ////////////////////////////content
                    if (is_null($content_en)) {
                        $tr->setTarget('en');
                        $content_en = $tr->translate($request->content);
                    }
                    if (is_null($content_fr)) {
                        $tr->setTarget('fr');
                        $content_fr = $tr->translate($request->content);
                    }
                    $buyer_ar = $request->buyer_instruct;
                    $content_ar = $request->content;
                    break;
                case 'en':
                    ////////buyer
                    if (is_null($buyer_ar)) {
                        $tr->setTarget('ar');
                        $buyer_ar = $tr->translate($request->buyer_instruct);
                    }
                    if (is_null($buyer_fr)) {
                        $tr->setTarget('fr');
                        $buyer_fr = $tr->translate($request->buyer_instruct);
                    }
                    //////////content
                    if (is_null($content_ar)) {
                        $tr->setTarget('ar');
                        $content_ar = $tr->translate($request->content);
                    }
                    if (is_null($content_fr)) {
                        $tr->setTarget('fr');
                        $content_fr = $tr->translate($request->content);
                    }

                    $buyer_en = $request->buyer_instruct;
                    $content_en = $request->content;
                    break;
                case 'fr':
                    /////////buyer
                    if (is_null($buyer_en)) {
                        $tr->setTarget('en');
                        $buyer_en = $tr->translate($request->buyer_instruct);
                    }
                    if (is_null($buyer_ar)) {
                        $tr->setTarget('ar');
                        $buyer_ar = $tr->translate($request->buyer_instruct);
                    }

                    ///////content
                    if (is_null($content_en)) {
                        $tr->setTarget('en');
                        $content_en = $tr->translate($request->content);
                    }
                    if (is_null($content_ar)) {
                        $tr->setTarget('ar');
                        $content_ar = $tr->translate($request->content);
                    }
                    $buyer_fr = $request->buyer_instruct;
                    $content_fr = $request->content;
                    break;
            }
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثالثة
            $data = [
                'buyer_instruct'  => $request->buyer_instruct,
                'buyer_instruct_ar' => $buyer_ar,
                'buyer_instruct_en' => $buyer_en,
                'buyer_instruct_fr' => $buyer_fr,
                'content'         => $request->content,
                'content_ar' => $content_ar,
                'content_en' => $content_en,
                'content_fr' => $content_fr,

            ];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_THREE) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_THREE;
            }
            // ============= انشاء المرحلة الثالثة في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_three"), $product);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function updateIsActive($id, ActiveProduct $request)
    {
        try {
            $is_active = $request->is_active;
            // جلب الخدمة
            $product = Product::select('id', 'is_active')
            ->ProductActive()
            ->whereId($id)
            ->where('is_vide', 0)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
            ->where('is_completed', Product::PRODUCT_IS_COMPLETED)
            ->where('is_draft', Product::PRODUCT_IS_NOT_DRAFT)
            ->first();
            // شرط اذا وجد هذه الخدمة
            if (!$product) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            if ($product->is_active == $is_active && $is_active== Product::PRODUCT_ACTIVE) {
                return response()->error(__("messages.seller.actived_product"), Response::HTTP_BAD_REQUEST);
            }
            else if ($product->is_active == $is_active && $is_active== Product::PRODUCT_REJECT) {
                return response()->error(__("messages.seller.disactived_product"), Response::HTTP_BAD_REQUEST);
            }
            /* -------------------- عملية تنشيط الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => $is_active]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            if ($is_active == Product::PRODUCT_ACTIVE)
            return response()->success(__("messages.seller.active_product"), $product);
            else
            return response()->success(__("messages.seller.disactive_product"), $product);
        } catch (Exception $ex) {
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete($id){
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            // ============================== حذف الخدمة ====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الخدمة
            $product->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ==============================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.delete_success"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    
    }
}
