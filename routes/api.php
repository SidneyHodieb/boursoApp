<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ApplicationController;


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

Route::post('/editItem/{tablename}',[ApplicationController::class,'editTable']);

Route::post('/runQuery',[ApplicationController::class,'runQuery']);





