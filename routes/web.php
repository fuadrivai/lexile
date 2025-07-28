<?php

use App\Http\Controllers\AIGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MyAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'prevent-back-history'], function () {
    Route::get('/', [GradeController::class, 'index'])->name('reading-test.index');
    Route::get('/reading/test', [GradeController::class, 'showTest'])->name('reading-test.show');
    Route::post('/reading/test', [GradeController::class, 'postAnswer'])->name('reading-test.post');
    Route::get('/ai/generator', [AIGeneratorController::class, 'index']);

    Route::get('/login', [MyAuthController::class, 'index'])->middleware('guest')->name('login');
    Route::post('/login', [MyAuthController::class, 'auth']);

    Route::group(['middleware' => 'check.token'], function () {
        Route::group(['prefix' => 'admin'], function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/history', [GradeController::class, 'history']);
            Route::get('/history/grade/{gradeId}', [GradeController::class, 'history_grade']);
            Route::get('/history/answere/{answerId}', [GradeController::class, 'answer_detail']);
        });
    });
});
