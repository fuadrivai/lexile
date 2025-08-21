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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/grade/{grade}/question', [App\Http\Controllers\GradeController::class, 'getQuestion']);
Route::get('/grade/question/{passage_id}', [App\Http\Controllers\GradeController::class, 'show']);
Route::post('/grade/question', [App\Http\Controllers\GradeController::class, 'store']);
Route::post('/grade/{grade}/question', [App\Http\Controllers\GradeController::class, 'postAnswer']);
