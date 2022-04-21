<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\JWTController;



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


Route::get('/tabledatas', [CustomersController::class, 'getTableDatas']);

Route::get('/customers/2',[CustomersController::class,'show']);

Route::get('/columnsnames',[CustomersController::class,'getTableColumns']);

Route::get('/alltables',[ApplicationController::class,'Alltables']);

Route::get('/alldirectories',[ApplicationController::class,'getDirectories']);

Route::get('/reportsnames',[ApplicationController::class,'getReportsNames']);

Route::post('/editItem/{tablename}',[ApplicationController::class,'editTable']);

Route::post('/runQuery',[ApplicationController::class,'runQuery']);

Route::group(['middleware' => 'api'], function($router) {
  Route::post('/register', [JWTController::class, 'register']);
  Route::post('/login', [JWTController::class, 'login']);
  Route::post('/logout', [JWTController::class, 'logout']);
  Route::post('/refresh', [JWTController::class, 'refresh']);
  Route::post('/profile', [JWTController::class, 'profile']);
});






