<?php

use App\Http\Controllers\AIGeneratorController;
use App\Http\Controllers\AIMathGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MyAuthController;
use App\Http\Controllers\StudentController;
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
    Route::get('/', [StudentController::class, 'index'])->name('student');
    Route::post('/', [StudentController::class, 'register'])->name('student.register');
    Route::get('/assessment', [StudentController::class, 'assessment'])->name('student.assessment');
    Route::get('/reading', [GradeController::class, 'index'])->name('reading-test.index');
    Route::get('/reading/test/{lexile_id}', [GradeController::class, 'showTest'])->name('reading-test.show');
    Route::get('/math/test/{lexile_id}', [GradeController::class, 'showTest'])->name('reading-test.show');
    Route::post('/reading/test', [GradeController::class, 'postAnswer'])->name('reading-test.post');
    Route::get('/ai/generator', [AIGeneratorController::class, 'index']);
    Route::get('/ai/generator/math', [AIMathGeneratorController::class, 'index']);

    Route::get('/login', [MyAuthController::class, 'index'])->middleware('guest')->name('login');
    Route::post('/login', [MyAuthController::class, 'auth']);
    Route::delete('/logout', [MyAuthController::class, 'logout']);

    Route::get('/export-answer/{grade}/{subject}', [GradeController::class, 'export_answer']);

    Route::group(['middleware' => 'check.token'], function () {
        Route::group(['prefix' => 'admin'], function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/history', [GradeController::class, 'history']);
            Route::get('/history/grade', [GradeController::class, 'history_grade']);
            Route::get('/history/answere/{answerId}', [GradeController::class, 'answer_detail']);
        });
    });
});
