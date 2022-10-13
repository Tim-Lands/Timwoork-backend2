<?php

use App\Http\Controllers\Me\ItemsController;
use App\Http\Controllers\Me\ProductController;
use App\Http\Controllers\Me\MeController;
use App\Http\Controllers\Me\WalletController as MeWalletController;
use Illuminate\Support\Facades\Route;
Route::get('/', [MeController::class,'index']);
Route::get('/wallet', [MeWalletController::class,'index']);
Route::get('/notifications', [MeController::class,'notifications']);
Route::get('/conversations',[MeController::class,'conversations']);
Route::get('/products/{type?}',[MeController::class,'products']);
Route::get('/profile',[MeController::class,'profile']);
Route::get('/profile/level',[MeController::class,'level']);
Route::get('/profile/badge',[MeController::class,'badge']);
Route::get('/items/purchases',[ItemsController::class,'indexPurchase']); 
Route::get('/items/sales',[ItemsController::class,'indexSells']);
Route::get('/items/purchases/{id}',[ItemsController::class,'showPurchase']); 
Route::get('/items/sales/{id}',[ItemsController::class,'showSales']); 
Route::PUT('/products/{id}/is_active',[ProductController::class,'updateIsActive']);
