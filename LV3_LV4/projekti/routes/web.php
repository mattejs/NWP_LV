<?php

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
use App\Models\Project;
use App\Models\ProjectUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;


Route::get('/', function () {
    return view('welcome');
});

/* Get users projects */
Route::get('/projects', [ProjectController::class, 'userProjects'])->name('userprojects');

/* Add a new project view */
Route::get('/newproject', [ProjectController::class, 'newProjectView'])->name('newproject');

/* Add a new project */
Route::post('/project', [ProjectController::class, 'newProject'])->name('project');

/* Edit project view */
Route::get('/editproject/{project_id?}', [ProjectController::class, 'editProjectView'])->name('editproject');

/* Save changes */
Route::put('/saveproject/{project_id?}', [ProjectController::class, 'editProject'])->name('saveproject');

/* View for adding users to project */
Route::get('/users/{project_id?}', [ProjectController::class, 'addUserOnProjectView'])->name('users');

/* Add user to project */
Route::post('/adduser', [ProjectController::class, 'addUserOnProject'])->name('adduser');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::auth();