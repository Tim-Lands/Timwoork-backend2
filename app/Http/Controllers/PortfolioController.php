<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePortfolioRequest;
use App\Models\PortfolioItems;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user'])->except('show', 'show1', 'index');
    }

    public function index(Request $request){
        try{
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;

        $portfolio_items = PortfolioItems::select('*')->paginate($paginate);
        return response()->success(__("messages.filter.filter_success"), $portfolio_items);
        }
        catch(Exception $exc){
            echo $exc;
        }
    }

    public function storePortfolio(ProfilePortfolioRequest $request)
    {
        $cover_Path = $request->file('cover');
        $coverName = 'tw-' . Auth::user()->id .  time() . '.' . $cover_Path->getClientOriginalExtension();
        // رفع الصورة
        $cover_Path->storePubliclyAs('portfolio_covers', $coverName, 'do');
        //$path = Storage::putFileAs('avatars', $request->file('avatar'), $avatarName);
        // تخزين اسم الصورة في قاعدة البيانات
        $profile_seller = Auth::user()->profile->profile_seller;
        // تغيير اسم المستخدم
        $cover_url = 'https://timwoork-space.ams3.digitaloceanspaces.com/portfolio_covers/' . $coverName;
        $profile_seller->portfolio_cover = $coverName;
        $profile_seller->portfolio_cover_url = $cover_url;
        $profile_seller->portfolio = $request->portfolio;
        $profile_seller->save();
    }

    public function add(Request $request)
    {
        try {
            
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default

            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->content);
            $xlocalization = $tr->getLastDetectedSource();
            if (!in_array($xlocalization, ['ar', 'fr', 'en']))
                $xlocalization = 'ar';
            $tr->setSource($xlocalization);

            $content_ar = $request->content_ar;
            $content_en = $request->content_en;
            $content_fr = $request->content_fr;
            $title_ar = $request->title_ar;
            $title_en = $request->title_en;
            $title_fr = $request->title_fr;

            $title = $request->title;
            $content = $request->content;

            switch ($xlocalization) {
                case "ar":
                    if (is_null($content_en)) {
                        $tr->setTarget('en');
                        $content_en = $tr->translate($request->content);
                        $title_en = $tr->translate($request->title);
                    }
                    if (is_null($content_fr)) {
                        $tr->setTarget('fr');
                        $content_fr = $tr->translate($request->content);
                        $title_fr = $tr->translate($request->title);
                    }
                    $content_ar = $request->content;
                    $title_ar = $request->title;
                    break;
                case 'en':
                    if (is_null($content_ar)) {
                        $tr->setTarget('ar');
                        $content_ar = $tr->translate($request->content);
                        $title_ar = $tr->translate($request->title);
                    }
                    if (is_null($content_fr)) {
                        $tr->setTarget('fr');
                        $content_fr = $tr->translate($request->content);
                        $title_fr = $tr->translate($request->title);
                    }
                    $content_en = $request->content;
                    $title_en = $request->title;
                    break;
                case 'fr':
                    if (is_null($content_en)) {
                        $tr->setTarget('en');
                        $content_en = $tr->translate($request->content);
                        $title_en = $tr->translate($request->title);
                    }
                    if (is_null($content_ar)) {
                        $tr->setTarget('ar');
                        $content_ar = $tr->translate($request->content);
                        $title_ar = $tr->translate($request->title);
                    }
                    $content_fr = $request->content;
                    $title_fr = $request->title;
                    break;
            }

            $request_tags = array_map(function ($key) {
                return json_decode($key);
            }, $request->tags);

            $tag_request_values = array_values(array_map(function ($key) {
                return strtolower($key->value);
            }, $request_tags));

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

            $cover_Path = $request->file('cover');
            $coverName = 'tw-' . Auth::user()->id .  time() . '.' . $cover_Path->getClientOriginalExtension();
            $cover_Path->storePubliclyAs('portfolio_covers', $coverName, 'do');
            $cover_url = 'https://timwoork-space.ams3.digitaloceanspaces.com/portfolio_covers/' . $coverName;


            DB::beginTransaction();
            $portfolio_item = PortfolioItems::create([
                'title' => $title,
                'content' => $content,
                'content_ar' => $content_ar,
                'content_en' => $content_en,
                'content_fr' => $content_fr,
                'title_ar' => $title_ar,
                'title_en' => $title_en,
                'title_fr' => $title_fr,
                'seller_id' => Auth::user()->profile->profile_seller->id,
                'cover_url' => $cover_url,
                'url'=>$request->url,
                'completed_date'=>$request->completed_date
            ]);
            $time = time();

            // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة
            if (!$request->images) {
                return response()->error(__("messages.product.count_galaries"), 403);
            }
            // عدد الصور التي تم رفعها
            if (count($request->file('images')) > 5 || count($request->file('images')) == 0) {
                return response()->error(__("messages.product.count_galaries"), 403);
            } 
            foreach ($request->file('images') as $key => $value) {
                $imagelName = "portfolio-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                // وضع المعلومات 
                $value->storePubliclyAs('portfolios/galaries-images', $imagelName, 'do');
                $galaries_images[$key] = [
                    'image_url' => "https://timwoork-space.ams3.digitaloceanspaces.com/portfolios/galaries-images/".$imagelName,

                ];
            }   

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
                $portfolio_item->tags()->sync($ids);
            } else {
                // اضافة وسوم التابع للخدمة
                $portfolio_item->tags()->sync($ids);
            }
            $portfolio_item->gallery()->createMany($galaries_images);
            DB::commit();
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            echo $exc;
            DB::rollBack();
        }
    }


    public function store_image(Request $request)
    {
        $time = time();
        // جلب الصور اذا كان هناك تعديل
        $id = $request->id;
        $portfolio_item = PortfolioItems::where('id', $id)->first();
        $get_galaries_images =  $portfolio_item->galaries;
        /* ---------------- معالجة الصور و الملفات و روابط الفيديوهات --------------- */
        // مصفوفة من اجل وضع فيها المعلومات الصور
        $galaries_images = [];

        // شرط اذا كانت هناك صورة مرسلة من قبل المستخدم
        if (count($get_galaries_images) != 0) {
            // شرط اذا كانت هناك صور ارسلت من قبل المستخدم
            if ($request->images) {
                // عدد الصور التي تم رفعها
                foreach ($request->file('images') as $key => $value) {
                    $imagelName = "portfolio-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
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
                    $image['full_path']->storePubliclyAs('portfolio/galaries-images', $image['path'], 'do');
                }
            }
        } else {
            // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة
            if (!$request->images) {
                return response()->error(__("messages.product.count_galaries"), 403);
            }
            // عدد الصور التي تم رفعها
            foreach ($request->file('images') as $key => $value) {
                $imagelName = "portfolio-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
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


        /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */
        // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
        DB::beginTransaction();
        // شرط اذا كانت توجد بيانات الصور في المصفوفة
        if (count($galaries_images) != 0) {
            // انشاء صور جديدة
            $portfolio_item->gallery()->createMany($galaries_images);
        }
        // انهاء المعاملة بشكل جيد :
        DB::commit();
        // ================================================================
        // رسالة نجاح عملية الاضافة:
        return response()->success(__("messages.product.success_upload_galaries"), $portfolio_item->load('gallery'));
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        PortfolioItems::where('id', $id)->delete();
        return response()->success(__("messages.oprations.get_all_data"));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $title = $request->title;
        $content = $request->content;
        PortfolioItems::where('id', $id)->update(['title' => $title, 'content' => $content]);
        return response()->success(__("messages.oprations.get_all_data"));
    }
}
