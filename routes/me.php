<?php

use App\Http\Controllers\MeController;
use Illuminate\Support\Facades\Route;
try{
Route::get('/', [MeController::class,'index']);
Route::get('/notifications', [MeController::class,'notifications']);
Route::get('/conversations',[MeController::class,'conversations']);
Route::get('/products/{type?}',[MeController::class,'products']);
Route::get('/profile',[MeController::class,'profile']);
Route::get('/profile/level',[MeController::class,'level']);
Route::get('/profile/badge',[MeController::class,'badge']);
}
catch(Exception $exc){
    echo($exc);
}