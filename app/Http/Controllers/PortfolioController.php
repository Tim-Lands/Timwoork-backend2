<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePortfolioRequest;
use App\Models\PortfolioItems;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{

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
            $title = $request->title;
            $content = $request->content;
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
            DB::beginTransaction();
            $portfolio_item = PortfolioItems::create([
                'title' => $title,
                'content' => $content,
                'seller_id' => Auth::user()->profile->profile_seller->id
            ]);
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
            DB::commit();
            return response()->success(__("messages.oprations.get_all_data"));
        } catch (Exception $exc) {
            echo $exc;
        }
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
