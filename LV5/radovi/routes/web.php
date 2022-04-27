<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/admin', [HomeController::class, 'admin'])->name('admin');
Route::post('/admin', [HomeController::class, 'changeRole'])->name('changeRole');

Route::get('/assignee/{studentId?}/{task?}', [TaskController::class, 'acceptAssignee'])->name('accept');
Route::get('/sign/{studentId?}/{task?}', [TaskController::class, 'taskSign'])->name('sign');

Route::group([
    'prefix' => '{locale?}', 
    'where' => ['locale' => 'en|hr'],
    'middleware' => 'locale'
], function() {
    Route::get('/new', [TaskController::class, 'index'])->name('create-task');
    Route::post('/new', [TaskController::class, 'store'])->name('create');
});