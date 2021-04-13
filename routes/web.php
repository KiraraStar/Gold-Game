<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoldGameController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware'=>'web'],function (){
   Route::any('/',[GoldGameController::class,'game']);
   Route::any('/login',[GoldGameController::class,'gameLogin']);
   Route::any('/score',[GoldGameController::class,'gameScore']);
   Route::any('/check',[GoldGameController::class,'gameCheck']);
   Route::any('/multiHome',[GoldGameController::class,'gameMulti']);
   Route::any('/multiHome/{homeid}',[GoldGameController::class,'gameHome']);
});
