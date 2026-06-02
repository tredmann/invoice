<?php

namespace App\Routes\Admin;

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'user-panel',
        'as' => 'user-panel.',
        'middleware' => ['auth', 'can:isAdmin,App\User'],
    ],
    function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}/demote', [UserController::class, 'demote'])->name('demote');
        Route::patch('/{user}/promote', [UserController::class, 'promote'])->name('promote');
    },
);
