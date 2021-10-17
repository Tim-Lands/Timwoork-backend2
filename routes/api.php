<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function (Request $request) {
    $data = [
        'status' => 200,
        'msg' => "لقد تمّ رفع الموقع بنجاح"
    ];
    return response()->json($data, 200);
});

Route::get('/tes', function (Request $request) {
    $data = [
        'status' => 200,
        'msg' => "test"
    ];
    return response()->json($data, 200);
});
