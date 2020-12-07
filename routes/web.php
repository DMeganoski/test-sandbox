<?php

use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Redirect;
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
    return Redirect::route('tasks.index');
});

Route::group(['prefix' => 'tasks'], function () {
    Route::get('/', [TasksController::class, 'index'])->name('tasks.index');
    Route::post('/store/', [TasksController::class, 'store'])->name('tasks.store');
    Route::get('/{task_id}/', [TasksController::class, 'read'])->name('tasks.read');
    Route::put('/{task_id}/{completed?}', [TasksController::class, 'update'])->name('tasks.update');
    Route::delete('/{task_id}', [TasksController::class, 'delete'])->name('tasks.delete');
});

