<?php

use App\Http\Controller\TaskController;
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

Route::prefix('task')->group(function() {
    Route::get('/show_tasks', [TaskController::class, 'showTasks']);
    Route::post('/create_task', [TaskController::class, 'createTask']);
    Route::patch('/update_task', [TaskController::class, 'updateTask']);

    // NOTE: lanjutkan tugas assignment di routing baru dibawah ini - Selesai;
    Route::delete('/delete_task', [TaskController::class, 'deleteTask']);
    Route::patch('/assign_task', [TaskController::class, 'assignTask']);
    Route::patch('/unassign_task', [TaskController::class, 'unassignTask']);
    Route::post('/create_subtask', [TaskController::class, 'createSubtask']);
    Route::delete('/delete_subtask', [TaskController::class, 'deleteSubtask']);
});
