<?php

use App\Http\Controllers\API\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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

Route::middleware('json.response')
    ->prefix('/inventory')
    ->group(function () {
        Route::get('/', 'InventoryController@index');
        Route::prefix('{id}')->group(function () {
            Route::patch('/', 'InventoryController@update');
            Route::post('/increment', 'InventoryController@increment');
            Route::post('/decrement', 'InventoryController@decrement');
        });
    });
