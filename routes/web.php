<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
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

Route::get('/',[HomeController::class,'index'])->name('login');
Route::get('register',[HomeController::class,'register'])->name('register');
Route::post('register', [HomeController::class, 'registerUser'])->name('userstore');

Route::post('/login', [HomeController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {

    Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('userlist',[DashboardController::class,'userlist'])->name('userlist');
    Route::post('adduser', [DashboardController::class, 'store'])->name('user.store');
    Route::get('deleteuser/{id}', [DashboardController::class, 'deleteuser'])->name('delete.user');
    Route::post('/admin/notifications/read/{id}', [DashboardController::class, 'markNotificationAsRead'])->name('admin.notifications.read');

    Route::get('tasklist',[DashboardController::class,'tasklist'])->name('tasklist');
    Route::post('/task/delete', [DashboardController::class, 'deleteTask'])->name('task.delete');
    Route::post('/task/update-status', [DashboardController::class, 'updateTaskStatus'])->name('task.update.status');

    Route::get('alltask',[DashboardController::class,'gettask_list'])->name('task_dt');
    Route::post('/task/create', [DashboardController::class, 'taskstore'])->name('task.store');
    Route::post('/task/update', [DashboardController::class, 'updateTask'])->name('task.update');

    Route::get('adduser',[DashboardController::class,'adduser'])->name('adduser');
    Route::get('addtask',[DashboardController::class,'addtask'])->name('addtask');

    Route::post('/user/update', [DashboardController::class, 'updateUser'])->name('user.update');

    Route::get('logout',[DashboardController::class,'logout'])->name('logout');

    

});
