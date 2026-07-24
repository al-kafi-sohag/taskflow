<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MyTaskController;
use App\Http\Controllers\User\ProjectController;
use App\Http\Controllers\User\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['as' => 'd.', 'prefix' => 'dashboard'], function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tasks', 'tasksData')->name('tasks');
        });
    });

    Route::group(['as' => 'mt.', 'prefix' => 'my-tasks'], function () {
        Route::controller(MyTaskController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tasks', 'tasksData')->name('tasks');
        });
    });

    Route::group(['as' => 'tasks.', 'prefix' => 'tasks'], function () {
        Route::controller(TaskController::class)->group(function () {
            Route::get('/form-options', 'formOptions')->name('form-options');
            Route::post('/', 'store')->name('store');
            Route::get('/{task}', 'show')->name('show');
            Route::get('/{task}/edit', 'edit')->name('edit');
            Route::put('/{task}', 'update')->name('update');
            Route::delete('/{task}', 'destroy')->name('destroy');
        });
    });

    Route::group(['as' => 'p.', 'prefix' => 'projects'], function () {
        Route::controller(ProjectController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{project}/edit', 'edit')->name('edit');
            Route::get('/{project}/tasks', 'tasksData')->name('tasks');
            Route::put('/{project}', 'update')->name('update');
            Route::delete('/{project}', 'destroy')->name('destroy');
            Route::get('/{project}', 'show')->name('show');
        });
    });
});

require __DIR__.'/auth.php';
