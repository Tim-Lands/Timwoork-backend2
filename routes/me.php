<?php

use App\Http\Controllers\Me\CartController;
use App\Http\Controllers\Me\ConversationController;
use App\Http\Controllers\Me\ItemsController;
use App\Http\Controllers\Me\ProductController;
use App\Http\Controllers\Me\MeController;
use App\Http\Controllers\Me\WalletController as MeWalletController;
use App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Route;
try{
    
Route::prefix('profile_seller')->group(function () {
    // اضافة بائع جديد
    Route::get('/',[SellerController::class,'index']);
    Route::post('/', [SellerController::class, 'store']);
    // اضافة تفاصيل بروفايل البائع
    Route::put('/details', [SellerController::class, 'detailsStore']);
    // اضافة المرحلة الاولى من بروفايل البائع
    Route::post('/step_one', [SellerController::class, 'step_one']);
    // اضافة المرحلة الثانية من بروفايل البائع
    Route::post('/step_two', [SellerController::class, 'step_two']);
});
Route::get('/', [MeController::class,'index']);
Route::get('/favourites', [MeController::class,'favourites']);
Route::get('/followers', [MeController::class,'followers']);
Route::get('/followings', [MeController::class,'followings']);
Route::get('/currency',[MeController::class,'currency']);
Route::get('/portfolio',[MeController::class,'portfolio']);
Route::get('/cart',[CartController::class, 'index']);
Route::put('/cart/items',[CartController::class,'store']);
Route::patch('/cart/items/{id}',[CartController::class,'update']);
Route::delete('/cart/items/{id}',[CartController::class,'delete']);
Route::get('/wallet', [MeWalletController::class,'index']);
Route::get('/notifications', [MeController::class,'notifications']);
Route::get("/unread_notifications_count",[MeController::class,'unread_notifications_count']);
Route::get('/conversations',[MeController::class,'conversations']);
Route::get('/conversations/{id}',[ConversationController::class,'show']);
Route::get('/unread_conversations_count',[MeController::class,'unread_conversations_count']);
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
Route::put('/status',[MeController::class,'status']);
Route::put('/products/{id}/step_one',[ProductController::class,'storeStepOne']);
Route::put('/products/{id}/step_two',[ProductController::class,'storeStepTwo']);
Route::put('/products/{id}/step_three',[ProductController::class,'storeStepThree']);
Route::put('/products/{id}/step_four',[ProductController::class,'storeStepFour']);
Route::put('/products/{id}/step_five',[ProductController::class,'storeStepFive']);
Route::put('/products/{id}/thumbnail',[ProductController::class,'upload_thumbnail']);
Route::put('/products/{id}/galary',[ProductController::class,'upload_galaries']);
Route::put('/products/{id}/conversations',[ConversationController::class,'create_conversation']);
Route::delete('products/gallary',[ProductController::class,'delete_gallery']);
Route::delete('/products/{id}',[ProductController::class,'delete']);

/* Route::get('/products',[ProductController::class],'index');
Route::get('/products/{id}',[ProductController::class, 'show']); */

Route::put('/products/{id}/is_active',[ProductController::class,'updateIsActive']);
}
catch(Exception $exc){
    echo("###############");
    echo($exc);
}