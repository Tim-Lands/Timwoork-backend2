<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Shortener;

class ShortenerController extends Controller
{
    public function __invoke($code)
    {
        $shortener = Shortener::where('code', $code)->first();
        if (!$shortener) {
            // رسالة خطأ
            return response()->error(__("messages.errors.url_not_found"), 403);
        }

        // الذهاب الى صفحة عرض الخدمة
        return redirect($shortener->url);
    }
}
