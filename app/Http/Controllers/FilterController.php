<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __invoke()
    {
        $res = Product::filter()->get();
        return response()->success('found', $res);
    }
}
