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

Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [App\Http\Controllers\MainController::class, 'getMainPage'])->name('getMainPage');
    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'subdivisions'], function ()
    {
        Route::get('/', [App\Http\Controllers\SubdivisionsController::class, 'getSubdivisionsPage']);
        Route::get('{id}', [App\Http\Controllers\SubdivisionsController::class, 'getSubdivisionPage']);
        Route::post('save', [App\Http\Controllers\SubdivisionsController::class, 'updateSubdiv']);
        Route::post('delete', [App\Http\Controllers\SubdivisionsController::class, 'deleteSubdiv']);
    });

    Route::group(['prefix' => 'projects'], function ()
    {
        Route::get('/', [App\Http\Controllers\ProjectsController::class, 'getProjectsPage']);
        Route::get('{id}', [App\Http\Controllers\ProjectsController::class, 'getProjectPage']);
        Route::post('save', [App\Http\Controllers\ProjectsController::class, 'saveProject']);
        Route::post('delete', [App\Http\Controllers\ProjectsController::class, 'deleteProject']);
    });

    Route::group(['prefix' => 'tasks'], function () {
        Route::get('create', [App\Http\Controllers\TasksController::class, 'newTaskPage']);
        Route::get('all', [App\Http\Controllers\MainController::class, 'getTasksPage'])->name('getTasksPage');

        Route::get('{id}', [App\Http\Controllers\TasksController::class, 'getTaskPage']);

        Route::post('get', [App\Http\Controllers\TasksController::class, 'getTasks']);
        Route::post('update_status', [App\Http\Controllers\TasksController::class, 'updateTaskStatus']);
        Route::post('save', [App\Http\Controllers\TasksController::class, 'saveTask']);
        Route::post('delete', [App\Http\Controllers\TasksController::class, 'deleteTask']);
    });

//    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//
//    Route::post('/home/tasks', [App\Http\Controllers\HomeController::class, 'getTasks'])->name('tasks');
//    Route::post('/home/tasks/edit', [App\Http\Controllers\HomeController::class, 'editTask'])->name('edit_task');
//    Route::post('/home/tasks/delete', [App\Http\Controllers\HomeController::class, 'deleteTask'])->name('delete_task');
//    Route::post('/home/tasks/create', [App\Http\Controllers\HomeController::class, 'createTask'])->name('create_task');
//    Route::post('/home/tasks/statistics', [App\Http\Controllers\HomeController::class, 'getTaskStatistics'])->name('task_statistics');
//
//    Route::get('/home/tasks/export', [App\Http\Controllers\HomeController::class, 'exportTasks'])->name('export_tasks')->middleware('role:manager');
//    Route::get('/home/tasks/statistics/export', [App\Http\Controllers\HomeController::class, 'exportStatistics'])->name('export_task_statistics')->middleware('role:manager');
//
//    Route::post('/home/comments', [App\Http\Controllers\HomeController::class, 'getComments'])->name('comments');
//    Route::post('/home/comments/edit', [App\Http\Controllers\HomeController::class, 'editComment'])->name('edit_comment');
//    Route::post('/home/comments/delete', [App\Http\Controllers\HomeController::class, 'deleteComment'])->name('delete_comment');
//    Route::post('/home/comments/create', [App\Http\Controllers\HomeController::class, 'createComment'])->name('create_comment');
//
//    Route::get('/home/comments/export', [App\Http\Controllers\HomeController::class, 'exportComments'])->name('export_comments')->middleware('role:manager');
//
//    Route::post('/home/employees/by_project', [App\Http\Controllers\HomeController::class, 'getProjectEmployees'])->name('project_employees');
});
