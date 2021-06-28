<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();
Route::group(['middleware' => 'auth'], function(){
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::post('/home/tasks', [App\Http\Controllers\HomeController::class, 'getTasks'])->name('tasks');
    Route::post('/home/tasks/edit', [App\Http\Controllers\HomeController::class, 'editTask'])->name('edit_task');
    Route::post('/home/tasks/delete', [App\Http\Controllers\HomeController::class, 'deleteTask'])->name('delete_task');

    Route::post('/home/comments', [App\Http\Controllers\HomeController::class, 'getComments'])->name('comments');
    Route::post('/home/comments/edit', [App\Http\Controllers\HomeController::class, 'editComment'])->name('edit_comment');
    Route::post('/home/comments/delete', [App\Http\Controllers\HomeController::class, 'deleteComment'])->name('delete_comment');
    Route::post('/home/comments/create', [App\Http\Controllers\HomeController::class, 'createComment'])->name('create_comment');
});
