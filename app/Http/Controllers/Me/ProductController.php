<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\Me\Product\ActiveProduct;
use App\Http\Requests\Products\ImagesRequest;
use App\Http\Requests\Products\ProductStepFourRequest;
use App\Http\Requests\Products\ProductStepOneRequest;
use App\Http\Requests\Products\ProductStepThreeRequest;
use App\Http\Requests\Products\ProductStepTwoRequest;
use App\Http\Requests\Products\ThumbnailRequest;
use App\Models\Category;
use App\Models\Galary;
use App\Models\Product;
use App\Models\Shortener;
use App\Models\Tag;
use App\Models\Video;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //
    public function index(Request $request, Response $response){
        try{
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
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
        ->select('id', "title_{$xlocalization} AS title", "slug_{$xlocalization} AS slug",
         'status', 'is_active', 'current_step', 'is_completed', 'is_draft','ratings_avg',
          'ratings_count', 'thumbnail',"buyer_instruct_{$xlocalization} AS buyer_instruct", "content_{$xlocalization} AS content", 'created_at', 'category_id')
          ->with([
            "subcategory"=>function($q) use($xlocalization){
                $q->select('id','parent_id', "name_{$xlocalization} AS name", "slug", "description_{$xlocalization} AS description", 'icon', 'image');
            },
            "subcategory.category"=>function($q) use($xlocalization){
                $q->select('id', "name_{$xlocalization} AS name", "slug", "description_{$xlocalization} AS description", 'icon', 'image');
            },
            ])  
          ->where($where[$type])
            ->whereNull($where_null[$type])
            ->paginate($paginate)
            ->makeHidden('ratings');
            
       
        
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
                            ->with([
                            
                            'subcategory'=>function($q) use($x_localization){
                                $q->select('id', 'parent_id' ,'slug', "name_{$x_localization} AS name");
                            },
                            'subcategory.category'=>function($q) use($x_localization){
                                $q->select('id', 'slug', "name_{$x_localization} AS name");
                            }    
                            ,'developments'=>function($q) use($x_localization) {$q->select('id',"title_{$x_localization} AS title",'product_id');}
                            ,'profileSeller'=>function($q) use($x_localization){
                                $q->select('id', "bio_{$x_localization} AS bio", 'profile_id', 'seller_badge_id', 'seller_level_id', 'created_at')->without(['paypal_account']);
                            }
                            ,'profileSeller.level'=>function($q) use($x_localization){
                                $q->select("id", "name_{$x_localization} AS name");
                            }
                            ,'profileSeller.badge'=>function($q) use($x_localization){
                                $q->select("id", "name_{$x_localization} AS name");
                            }
                            ,'product_tag','galaries','file','video','shortener','ratings'=>function($q) use($x_localization){
                                $q->select('id', 'user_id', 'product_id', 'rating', "comment_{$x_localization} AS comment",'created_at');
                            }
                            ,'ratings.user','ratings.user.profile'=>function($q){
                                $q->select('*')->without(['paypal_account','wise_account', 'bank_account', 'bank_transfer_detail']);
                            }
                            ,'ratings.user.profile.badge'=>function($q) use($x_localization){
                                $q->select("id", "name_{$x_localization} AS name");
                            },
                            'ratings.user.profile.level'=>function($q) use($x_localization){
                                $q->select("id", "name_{$x_localization} AS name");
                            }
                            ])
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
                'slug_ar'           =>$product->id.'-'. Str::slug($title_ar),
                'slug_en'           =>$product->id.'-'. Str::slug($title_en),
                'slug_fr'           =>$product->id.'-'. Str::slug($title_fr),
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
            $product_json = (object)$product
            ->load(['developments'=>function($q) use($xlocalization) {$q->select('id',"title_{$xlocalization} AS title",'product_id');}])
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at','developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
            return response()
            ->success(__("messages.product.success_step_one"),
             $product_json);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            echo $ex;
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
                    $tr->translate($request->developments[0]['title']);
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
            $product_json = (object)$product
            ->load(['developments'=>function($q) use($xlocalization) {$q->select('id',"title_{$xlocalization} AS title",'product_id');}])
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at', 'developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_two"), $product_json);
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
            $product_json = (object)$product
            ->load(['developments'=>function($q) use($xlocalization) {$q->select('id',"title_{$xlocalization} AS title",'product_id');}])
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at', 'developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_three"), $product_json);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function storeStepFour(mixed $id, ProductStepFourRequest $request)
    {
        try {

            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                ->with(['galaries', 'video'])
                ->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            if (count($product->galaries) == 0 || $product->thumbnail == null) {
                // رسالة خطأ
                return response()->error(__("messages.errors.upload_images"), 422);
            }

            $data = [];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_FOUR) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_FOUR;
            }

            // جلب رابط الفيديو
            $get_galaries_url_video =  $product->video;


            // ====================== انشاء المرحلة الرابعة في الخدمة =====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // تعديل على الخدمة
            //$product->update($data);
            // شرط اذا كانت هناك ارسال رابط في فيديو من قبل المستخدم
            if ($request->has('url_video')) {
                //return 1;
                // شرط اذا كانت توجد بيانات رابط الفيديو من قبل
                if ($get_galaries_url_video != null) {
                    // عملية التعديل على رابط الفيديو
                    $product->video()->update([
                        'url_video' => $request->url_video
                    ]);
                } else {
                    // انشاء رابط فيديو جديد
                    Video::create([
                        'url_video' => $request->url_video,
                        'product_id' => $product->id
                    ]);
                }
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_four"), $product->load('video'));
            // ========================================================
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * storeStepFive => => دالة انشاء المرحلة الخامسة من الخدمة
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function storeStepFive($id, Request $request): JsonResponse
    {
        try {
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
                exit();
            }
            // شرط هل يوجد رابط مختصر من قبل
            $shorterner = Shortener::whereProductId($id)->exists();
            // شرط اذا كان لا يوجد رابط مختصر
            if (!$shorterner) {
                // وضع معلومات في مصفوفة من اجل عملية الانشاء رابط مختصر
                $data_shortener = [
                    'code'  => Str::random(7),
                    'url'  => "http://timwoork.test/api/product/{$product['slug']}"
                ];
            }
            //  وضع معلومات في مصفوفة من اجل عملية الانشاء المرحلة الخامسة
            $data = [
                'is_draft'      => Product::PRODUCT_IS_NOT_DRAFT,
                'current_step'  => Product::PRODUCT_STEP_FIVE,
                'is_completed'  => Product::PRODUCT_IS_COMPLETED,
                'is_active'     => Product::PRODUCT_ACTIVE,
            ];
            // ============= انشاء المرحلة الاخيرة في الخدمة و نشرها ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // شرط هل يوجد رابط مختصر من قبل
            if (!$shorterner) {
                $product->shortener()->create($data_shortener);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $product_json = (object)$product
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at', 'developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
           
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_final"), $product_json);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function updateIsActive($id, Request $request)
    {
        try {
            if(strtolower($request->is_active) == "true" || $request->is_active==1)
                $is_active=1;
            else
                $is_active = 0;
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
    public function delete_gallery($id, Request $request){
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                ->with('galaries')
                ->first();

            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // galaries is count 1
            if ($product->galaries->count() == 1) {
                return response()->error(__("messages.product.count_galaries"), 403);
            }
            // جلب الصورة من المعرض
            $galary = Galary::whereId($request->id)->where('product_id', $id)->first();
            // تحقق من صورة موجودة
            if (!$galary || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            /* ---------------- معالجة الصور و الملفات و روابط الفيديوهات --------------- */

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                // حذف صورة السابقة
                if (Storage::disk('do')->exists("products/galaries-images/{$galary->path}")) {
                    Storage::disk('do')->delete("products/galaries-images/{$galary->path}");
                }

                // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
                DB::beginTransaction();
                // حذف الصورة المعرض
                $galary->delete();
                // انهاء المعاملة بشكل جيد :
                DB::commit();
                // رسالة نجاح عملية الاضافة:
                return response()->success(__("messages.product.delete_galary"), $galary);
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
            }
            /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */

            // ================================================================
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function upload_thumbnail($id, ThumbnailRequest $request)
    {
        try {
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع مصفوفة من اجل عملية التعديل
            $data_thumbnail = [];
            /* ------------------------- معالجة الصورة الامامية ------------------------- */

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                $time = time();
                // شرط في حالة ما اذا كانت الصورة مرسلة من المستخدم
                if ($product->thumbnail) {
                    // شرط اذا قام المستخدم بأرسال صورة الامامية
                    if ($request->thumbnail) {
                        // حذف صورة السابقة
                        if (Storage::disk('do')->exists("products/thumbnails/{$product->thumbnail}")) {
                            Storage::disk('do')->delete("products/thumbnails/{$product->thumbnail}");
                        }
                        //Storage::delete("products/thumbnails/{$product->thumbnail}");
                        // جلب الصورة من المرسلات
                        $thumbnailPath = $request->file('thumbnail');
                        // وضع اسم جديد للصورة
                        $thumbnailName = "tw-thumbnail-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                        // رفع الصورة الامامية للخدمة
                        //Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                        $thumbnailPath->storePubliclyAs('products/thumbnails', $thumbnailName, 'do');
                        // وضع اسم الصورة في المصفوفة

                        $data_thumbnail['thumbnail'] = $thumbnailName;
                    }
                } elseif ($request->thumbnail) {
                    // جلب الصورة من المرسلات
                    $thumbnailPath = $request->file('thumbnail');
                    // وضع اسم جديد للصورة
                    $thumbnailName = "tw-thumbnail-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                    // رفع الصورة الامامية للخدمة
                    $thumbnailPath->storePubliclyAs('products/thumbnails', $thumbnailName, 'do');
                    //Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                    // وضع اسم الصورة في المصفوفة
                    $data_thumbnail['thumbnail'] = $thumbnailName;
                } else {
                    return response()->error(__("messages.product.thumbnail_required"), 403);
                }
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
            }
            /* ---------------------- رفع الصورة على قواعد البيانات --------------------- */
            //بداية المعاملة مع البيانات المرسلة لقاعدة بيانات:
            DB::beginTransaction();
            // عملية التعديل او انشاء الصورة
            $product->update($data_thumbnail);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $product_json = (object)$product
            ->load('developments')
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at', 'developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
           
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_upload_thumbnail"), $product_json);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function upload_galaries($id, ImagesRequest $request)
    {
        try {
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                ->with('galaries')
                ->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وقت رفع الصورة
            $time = time();
            // جلب الصور اذا كان هناك تعديل
            $get_galaries_images =  $product->galaries;
            /* ---------------- معالجة الصور و الملفات و روابط الفيديوهات --------------- */
            // مصفوفة من اجل وضع فيها المعلومات الصور
            $galaries_images = [];

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                // شرط اذا كانت هناك صورة مرسلة من قبل المستخدم
                if (count($get_galaries_images) != 0) {
                    // شرط اذا كانت هناك صور ارسلت من قبل المستخدم
                    if ($request->images) {
                        // عدد الصور التي تم رفعها
                        foreach ($request->file('images') as $key => $value) {
                            $imagelName = "tw-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                            // وضع المعلومات فالمصفوفة
                            $galaries_images[$key] = [
                                'path'      => $imagelName,
                                'full_path' => $value,
                                'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                                'mime_type' => $value->getClientOriginalExtension(),
                            ];
                        }
                        // عملية رفع المفات
                        foreach ($galaries_images as $image) {
                            // رفع الصور
                            $image['full_path']->storePubliclyAs('products/galaries-images', $image['path'], 'do');
                        }
                    }
                } else {
                    // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة
                    if (!$request->images) {
                        return response()->error(__("messages.product.count_galaries"), 403);
                    }
                    // عدد الصور التي تم رفعها
                    foreach ($request->file('images') as $key => $value) {
                        $imagelName = "tw-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                        // وضع المعلومات فالمصفوفة
                        $galaries_images[$key] = [
                            'path'      => $imagelName,
                            'full_path' => $value,
                            'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                            'mime_type' => $value->getClientOriginalExtension(),
                        ];
                    }
                    // شرط اذا كان عدد صور يزيد عند 5 و يقل عن 1
                    if (count($galaries_images) > 5 || count($galaries_images) == 0) {
                        return response()->error(__("messages.product.count_galaries"), 403);
                    } else {
                        // عملية رفع المفات
                        foreach ($galaries_images as $image) {
                            // رفع الصور
                            $image['full_path']->storePubliclyAs('products/galaries-images', $image['path'], 'do');
                        }
                    }
                }
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
            }

            /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // شرط اذا كانت توجد بيانات الصور في المصفوفة
            if (count($galaries_images) != 0) {
                // انشاء صور جديدة
                $product->galaries()->createMany($galaries_images);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $product_json = (object)$product
            ->load('galaries')
            ->only('id',"title_{$xlocalization}",
             "slug_{$xlocalization}","content_{$xlocalization}",
             'price','duration','full_path_thumbnail',"buyer_instruct_{$xlocalization}",
              'status', 'is_active','current_step','is_completed','is_draft','category_id','is_vide','ratings_avg',
              'ratings_count','deleted_at', 'developments');
              

              $slug_xlocalization = "slug_{$xlocalization}";
              $title_xlocalization = "title_{$xlocalization}";
              $content_xlocalization = "content_{$xlocalization}";
              $buyer_instruct_xlocalization = "buyer_instruct_{$xlocalization}";
              $product_json->slug = $product->$slug_xlocalization;
              $product_json->title = $product->$title_xlocalization;
              $product_json->content = $product->$content_xlocalization;
              $product_json->buyer_instruct = $buyer_instruct_xlocalization;
              unset($product_json->$slug_xlocalization, $product_json->$title_xlocalization, $product_json->$content_xlocalization, $product_json->$buyer_instruct_xlocalization);
           
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_upload_galaries"), $product_json);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
