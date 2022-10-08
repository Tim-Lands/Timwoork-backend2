<?php

use App\Http\Controllers\Me\ItemsController;
use App\Http\Controllers\Me\ProductController;
use App\Http\Controllers\Me\MeController;
use Illuminate\Support\Facades\Route;
try{
Route::get('/', [MeController::class,'index']);
Route::get('/notifications', [MeController::class,'notifications']);
Route::get('/conversations',[MeController::class,'conversations']);
Route::get('/products/{type?}',[MeController::class,'products']);
Route::get('/profile',[MeController::class,'profile']);
Route::get('/profile/level',[MeController::class,'level']);
Route::get('/profile/badge',[MeController::class,'badge']);
Route::get('/items',[ItemsController::class,'index']); 
Route::PUT('/products/{id}/is_active',[ProductController::class,'updateIsActive']);
}
catch(Exception $exc){
    echo("###############");
    echo($exc);
}