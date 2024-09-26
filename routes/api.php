<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\APiMasterController;
use App\Http\Controllers\Api\LoginController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [LoginController::class, 'login']); // Login and generate API token

Route::middleware('auth:sanctum')->group(function () {
    // Task Management Routes
    Route::post('/logout', [APiMasterController::class, 'logout']);

    Route::get('/tasks', [ApiMasterController::class, 'getTasks']); // Retrieve all tasks or user-specific tasks

    Route::post('/tasks', [ApiMasterController::class, 'createTask']); // Create a new task
    Route::put('/tasks/{id}', [ApiMasterController::class, 'updateTaskStatus']); // Update task status
    Route::delete('/tasks/{id}', [ApiMasterController::class, 'deleteTask']); // Delete a task

    // Task Statistics
    Route::get('/tasks/statistics', [ApiMasterController::class, 'getTaskStatistics']); // Get task statistics

    // Assign task to user (admin only)
    Route::post('/tasks/assign', [ApiMasterController::class, 'assignTask']); // Assign task to a user (admin only)
});
