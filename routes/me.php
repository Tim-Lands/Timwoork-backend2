<?php

use App\Http\Controllers\Me\ItemsController;
use App\Http\Controllers\Me\ProductController;
use App\Http\Controllers\Me\MeController;
use App\Http\Controllers\Me\WalletController as MeWalletController;
use Illuminate\Support\Facades\Route;
try{
Route::get('/', [MeController::class,'index']);
Route::get('/wallet', [MeWalletController::class,'index']);
Route::get('/notifications', [MeController::class,'notifications']);
Route::get('/conversations',[MeController::class,'conversations']);
Route::get('/profile',[MeController::class,'profile']);
Route::get('/profile/level',[MeController::class,'level']);
Route::get('/profile/badge',[MeController::class,'badge']);
Route::get('/items/purchases',[ItemsController::class,'indexPurchase']); 
Route::get('/items/sales',[ItemsController::class,'indexSells']);
Route::get('/items/purchases/{id}',[ItemsController::class,'showPurchase']); 
Route::get('/items/sales/{id}',[ItemsController::class,'showSales']); 
Route::get('/products',[ProductController::class,'index']);
Route::get('/products/{id}',[ProductController::class,'show']);
Route::post('/products',[ProductController::class,'store']);
Route::put('/products/step_one',[ProductController::class,'storeStepOne']);
Route::put('/products/step_two',[ProductController::class,'storeStepTwo']);
Route::put('/products/step_three',[ProductController::class,'storeStepThree']);
Route::delete('/products/{id}',[ProductController::class,'delete']);

/* Route::get('/products',[ProductController::class],'index');
Route::get('/products/{id}',[ProductController::class, 'show']); */

Route::PUT('/products/{id}/is_active',[ProductController::class,'updateIsActive']);
}
catch(Exception $exc){
    echo("###############");
    echo($exc);
}