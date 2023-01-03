<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioAddRequest;
use App\Http\Requests\PortfolioUpdateRequest;
use App\Http\Requests\ProfilePortfolioRequest;
use App\Models\Category;
use App\Models\PortfolioGallery;
use App\Models\PortfolioItems;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user'])->except('show', 'show1', 'index', 'indexByUser');
    }

    public function index(Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
            $id = null;
            $curr_user = $request->user('sanctum');
            if ($curr_user)
                $id = $curr_user->profile->id;
            $is_user = is_null($id);
            $portfolio_items = $is_user ? PortfolioItems::select(
                'id',
                'created_at',
                'seller_id',
                "content_{$x_localization} AS content",
                "title_{$x_localization} AS title",
                'cover_url',
                'url',
                'completed_date',
            )
            ->filter('category_id')

                ->with([
                    'gallery',
                    'subcategory' => function ($q) use ($x_localization) {
                        $q->select('id', "slug", 'icon', 'image', 'parent_id', "name_{$x_localization} AS name");
                    },
                    "seller" => function ($q) use ($x_localization) {
                        $q->select('id', 'profile_id', "bio_{$x_localization} AS bio");
                    },
                    'seller.profile' => function ($q) use ($x_localization) {
                        $q->select('id', 'first_name', 'last_name', 'avatar', 'avatar_url', 'full_name', 'level_id')
                            ->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_details']);
                    },
                    'seller.profile.level' => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name");
                    }
                ])
                ->withCount([
                    'likers',
                    'fans',
                ])
                ->paginate($paginate)
                :
                PortfolioItems::select(
                    'id',
                    'created_at',
                    'seller_id',
                    "content_{$x_localization} AS content",
                    "title_{$x_localization} AS title",
                    'cover_url',
                    'url',
                    'completed_date',
                )
                ->filter('category_id')
                ->with([
                    'gallery',
                    'subcategory' => function ($q) use ($x_localization) {
                        $q->select('id', "slug", 'icon', 'image', 'parent_id', "name_{$x_localization} AS name");
                    },
                    "seller" => function ($q) use ($x_localization) {
                        $q->select('id', 'profile_id', "bio_{$x_localization} AS bio");
                    },
                    'seller.profile' => function ($q) use ($x_localization) {
                        $q->select('id', 'first_name', 'last_name', 'avatar', 'avatar_url', 'full_name', 'level_id')
                            ->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_details']);
                    },
                    'seller.profile.level' => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name");
                    }
                ])
                ->withCount([
                    'likers',
                    'fans',
                ])
                ->withExists([
                    'likers AS is_liked' => function ($q) use ($id) {
                        $q->where('profile_id', $id);
                    },
                    'fans AS is_favourite' => function ($q) use ($id) {
                        $q->where('profile_id', $id);
                    }
                ])
                ->paginate($paginate);
            return response()->success(__("messages.filter.filter_success"), $portfolio_items);
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function indexByUser($username, Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $id = null;
            $curr_user = $request->user('sanctum');
            if ($curr_user)
                $id = $curr_user->profile->id;
            $user = !is_null($id)
                ? User::where('username', $username)->orWhere('id', $username)
                ->select("id")
                ->with([
                    'profile' => function ($q) {
                        $q->select('id', 'user_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                    },
                    'profile.profile_seller' => function ($q) {
                        $q->select('id', 'profile_id');
                    },
                    'profile.profile_seller.portfolio_items' => function ($q) use ($id, $x_localization) {
                        $q->select(
                            'id',
                            'created_at',
                            'updated_at',
                            'seller_id',
                            "content_{$x_localization} AS content",
                            "title_{$x_localization} AS title",
                            'cover_url',
                            'url',
                            'completed_date',
                            'category_id'
                        )
                            ->with(['subcategory' => function ($q) use ($x_localization) {
                                $q->select('id', "slug", 'icon', 'image', 'parent_id', "name_{$x_localization} AS name");
                            },])
                            ->withCount(['likers', 'fans'])
                            ->withExists([
                                'likers AS is_liked' => function ($q) use ($id) {
                                    $q->where('profile_id', $id);
                                },
                                'fans AS is_favourite' => function ($q) use ($id) {
                                    $q->where('profile_id', $id);
                                }
                            ]);
                    }
                ])
                ->first()
                : User::where('username', $username)->orWhere('id', $username)
                ->select("id")
                ->with([

                    'profile' => function ($q) {
                        $q->select('id', 'user_id')->without(['paypal_account', 'wise_account', 'bank_account', 'bank_transfer_detail']);
                    },
                    'profile.profile_seller' => function ($q) {
                        $q->select('id', 'profile_id');
                    },
                    'profile.profile_seller.portfolio_items' => function ($q) use ($id, $x_localization) {
                        $q->select(
                            'id',
                            'created_at',
                            'updated_at',
                            'seller_id',
                            "content_{$x_localization} AS content",
                            "title_{$x_localization} AS title",
                            'cover_url',
                            'url',
                            'completed_date',
                            'category_id'
                        )
                            ->with(['subcategory' => function ($q) use ($x_localization) {
                                $q->select('id', "slug", 'icon', 'image', 'parent_id', "name_{$x_localization} AS name");
                            },])
                            ->withCount(['likers', 'fans']);
                    }
                ])
                ->first();

            if (!$user)
                return response()->error(__("messages.errors.element_not_found"));
            $portfolio_items = $user->profile->profile_seller->portfolio_items;
            return $portfolio_items;

            //return $portfolio_items;
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function show($id, Request $request)
    {
        try {
            $x_localization = 'ar';
            if ($request->hasHeader('X-localization')) {
                $x_localization = $request->header('X-localization');
            }
            $portfolio_item = PortfolioItems::select(
                'id',
                'created_at',
                'seller_id',
                "content_{$x_localization} AS content",
                "title_{$x_localization} AS title",
                'cover_url',
                'url',
                'completed_date',
                'category_id'
            )->where('id', $id)
                ->with([
                    'gallery',
                    'subcategory' => function ($q) use ($x_localization) {
                        $q->select('id', "slug", 'icon', 'image', 'parent_id', "name_{$x_localization} AS name");
                    },
                    "seller" => function ($q) use ($x_localization) {
                        $q->select('id', 'profile_id', "bio_{$x_localization} AS bio");
                    },
                    'seller.profile' => function ($q) use ($x_localization) {
                        $q->select('id', 'first_name', 'last_name', 'avatar', 'avatar_url', 'full_name', 'level_id')->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_details']);
                    },
                    "seller.profile.level" => function ($q) use ($x_localization) {
                        $q->select('id', "name_{$x_localization} AS name");
                    },
                    'portfolio_item_tags'
                ])
                ->withCount(['likers', 'fans'])
                ->first();

            if (!$portfolio_item)
                return response()->error(__("messages.errors.element_not_found"));

            //$like = Auth::user()->profile->liked_portfolios->contains($id);
            $is_liked = false;
            $is_favourite = false;
            $user = $request->user('sanctum');
            if ($user) {
                $profile = $user->profile;
                $buyer_profile = $portfolio_item->seller->profile;
                $is_liked = $portfolio_item->likers->contains($user->id);
                $is_favourite = $portfolio_item->fans->contains($user->id);
                if ($profile->id == $buyer_profile->id){
                    $views = $portfolio_item->viewers->count();
                    $portfolio_item->viewers_count = $views;
                }
                else
                    $portfolio_item->viewers()->attach($user->id);
            }
            $portfolio_item->is_liked = $is_liked;
            $portfolio_item->is_favourite = $is_favourite;
            return response()->success(__("messages.oprations.get_data"), $portfolio_item);
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function add(PortfolioAddRequest $request)
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
            $subcategory = Category::child()->where('id', $request->subcategory)->exists();
            // التحقق اذا كان موجود ام لا
            if (!$subcategory) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            if (is_null($request->tags))
                $request->tags = array();

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
                'url' => $request->url,
                'completed_date' => $request->completed_date,
                'category_id'       =>  (int)$request->subcategory,

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
                    'image_url' => "https://timwoork-space.ams3.digitaloceanspaces.com/portfolios/galaries-images/" . $imagelName,

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
                $portfolio_item->portfolio_item_tags()->sync($ids);
            } else {
                // اضافة وسوم التابع للخدمة
                $portfolio_item->portfolio_item_tags()->sync($ids);
            }
            $portfolio_item->gallery()->createMany($galaries_images);
            DB::commit();
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            echo $exc;
            DB::rollBack();
        }
    }

    public function update(PortfolioUpdateRequest $request)
    {
        try {
            $id = $request->id;
            $seller_id = Auth::user()->profile->profile_seller->id;
            $portfolio_item = PortfolioItems::where('id', $id)->first();
            if (!$portfolio_item)
                return response()->error(__("messages.errors.element_not_found"));

            if ($portfolio_item->seller_id != $seller_id) {
                return response(403);
            }

            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default



            $content_ar = $request->content_ar;
            $content_en = $request->content_en;
            $content_fr = $request->content_fr;
            $title_ar = $request->title_ar;
            $title_en = $request->title_en;
            $title_fr = $request->title_fr;
            $title = $request->title;
            $content = $request->content;
            $cover_url = null;
            if ($request->title || $request->content) {
                $tr->setSource();
                $tr->setTarget('en');
                $tr->translate(
                    $request->content
                        ? $request->content
                        : $request->title

                );
                $xlocalization = $tr->getLastDetectedSource();
                if (!in_array($xlocalization, ['ar', 'fr', 'en']))
                    $xlocalization = 'ar';
                $tr->setSource($xlocalization);

                switch ($xlocalization) {
                    case "ar":
                        if (is_null($content_en)) {
                            $tr->setTarget('en');

                            $content_en = $request->content ? $tr->translate($request->content) : $content_en;

                            $title_en = $request->title ? $tr->translate($request->title) : $title_en;
                        }

                        if (is_null($content_fr)) {
                            $tr->setTarget('fr');
                            $content_fr = $request->content ? $tr->translate($request->content) : $content_fr;
                            $title_fr = $request->title ? $tr->translate($request->title) : $title_fr;
                        }

                        $content_ar = $request->content;
                        $title_ar = $request->title;

                        break;
                    case 'en':
                        if (is_null($content_ar)) {
                            $tr->setTarget('ar');
                            $content_ar = $request->content ? $tr->translate($request->content) : $content_ar;
                            $title_ar = $request->title ? $tr->translate($request->title) : $title_ar;
                        }
                        if (is_null($content_fr)) {
                            $tr->setTarget('fr');
                            $content_fr = $request->content ? $tr->translate($request->content) : $content_fr;
                            $title_fr = $request->title ? $tr->translate($request->title) : $title_fr;
                        }
                        $content_en = $request->content;
                        $title_en = $request->title;
                        break;
                    case 'fr':
                        if (is_null($content_en)) {
                            $tr->setTarget('en');
                            $content_en = $request->content ? $tr->translate($request->content) : $content_en;
                            $title_en = $request->title ? $tr->translate($request->title) : $title_en;
                        }
                        if (is_null($content_ar)) {
                            $tr->setTarget('ar');
                            $content_ar = $request->content ? $tr->translate($request->content) : $content_ar;
                            $title_ar = $request->title ? $tr->translate($request->title) : $title_ar;
                        }
                        $content_fr = $request->content;
                        $title_fr = $request->title;
                        break;
                }
            }
            if (is_null($request->tags))
                $request->tags = array();

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
            if (!is_null($request->file('cover'))) {
                $cover_Path = $request->file('cover');
                $coverName = 'tw-' . Auth::user()->id .  time() . '.' . $cover_Path->getClientOriginalExtension();
                $cover_Path->storePubliclyAs('portfolio_covers', $coverName, 'do');
                $cover_url = 'https://timwoork-space.ams3.digitaloceanspaces.com/portfolio_covers/' . $coverName;
            }

            DB::beginTransaction();
            $data = [
                'title' => $title  ? $title : $portfolio_item->title,
                'content' => $content  ? $content : $portfolio_item->content,
                'content_ar' => $content_ar  ? $content_fr : $portfolio_item->content_ar,
                'content_en' => $content_en  ? $content_en : $portfolio_item->content_en,
                'content_fr' => $content_fr  ? $content_fr : $portfolio_item->content_fr,
                'title_ar' => $title_ar  ? $title_ar : $portfolio_item->title_ar,
                'title_en' => $title_en  ? $title_en : $portfolio_item->title_en,
                'title_fr' => $title_fr ? $title_fr : $portfolio_item->title_fr,
                'cover_url' => $cover_url ? $cover_url : $portfolio_item->cover_url,
                'url' => $request->url ? $request->url : $portfolio_item->url,
                'completed_date' => $request->completed_date ? $request->completed_date : $portfolio_item->completed_date,
                'category_id'  => $request->subcategory ? (int)$request->subcategory : $portfolio_item->category_id,

            ];
            $portfolio_item->update($data);
            $time = time();

            // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة   
            if ($request->file('images')) {

                $get_galaries_images =  $portfolio_item->gallery;
                if ((count($request->file('images'))) > 5 || count($request->file('images')) == 0) {
                    return response()->error(__("messages.product.count_galaries"), 403);
                }

                foreach ($request->file('images') as $key => $value) {
                    $imagelName = "portfolio-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                    // وضع المعلومات 
                    $value->storePubliclyAs('portfolios/galaries-images', $imagelName, 'do');
                    $galaries_images[$key] = [
                        'image_url' => "https://timwoork-space.ams3.digitaloceanspaces.com/portfolios/galaries-images/" . $imagelName,

                    ];
                }
                $portfolio_item->gallery()->createMany($galaries_images);
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
                $portfolio_item->portfolio_item_tags()->sync($ids);
            } else {
                // اضافة وسوم التابع للخدمة
                $portfolio_item->portfolio_item_tags()->sync($ids);
            }
            DB::commit();
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            echo $exc;
            DB::rollBack();
        }
    }

    public function delete($id, Request $request)
    {
        $portfolio_item = PortfolioItems::where('id', $id)->first();
        $portfolio_user_id = $portfolio_item->seller->profile->user->id;
        $auth_id = Auth::user()->id;
        if ($portfolio_user_id != $auth_id)
            return response()->error(__("messages.errors.element_not_found"));
        $portfolio_item->delete();
        return response()->success(__("messages.oprations.get_all_data"));
    }

    public function deleteImage($id, Request $request)
    {
        try {
            DB::enableQueryLog();
            $image = PortfolioGallery::where('id', $id)
                /*->with([
                'portfolio_item',
                'portfolio_item.seller',
                'portfolio_item.seller.profile'
                ])
            //->where('portfolio_item_id','=',8)
            /*->whereHas('portfolio_item',function($q){
                $q->where('id',8);
            })*/
                ->first();
            //return $image;
            if (!$image)
                return response()->error(__("messages.errors.element_not_found"));
            $image_user_id = $image->portfolio_item->seller->profile->user->id;
            $auth_id = Auth::user()->id;
            if ($image_user_id != $auth_id)
                return response()->error(__("messages.errors.element_not_found"));
            $image->delete();
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            return response()->error(__("messages.errors.error_database"));
        }
    }

    public function favourite($id, Request $request)
    {
        try {
            $profile = Auth::user()->profile;
            $profile->favourites()->toggle($id);
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            return response()->error(__("messages.errors.element_not_found"));
        }
    }

    public function like($id, Request $request)
    {
        try {
            $profile = Auth::user()->profile;
            $profile->liked_portfolios()->toggle($id);
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            return response()->error(__("messages.errors.element_not_found"));
        }
    }


    public function view($id, Request $request)
    {
        try {
            $profile = Auth::user()->profile;
            $viewed_portfolios  = $profile->viewed_portfolios;
            if ($viewed_portfolios->contains($id))
                return response()->success(__("messages.oprations.get_all_data"));
            $profile->viewed_portfolios()->attach($id);
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            echo $exc;
            return response()->error(__("messages.errors.element_not_found"));
        }
    }
}
