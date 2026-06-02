<?php

use App\Http\Controllers\ApiTokenManager\ApiTokenManagerController;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\CurrentTeamController;
use Laravel\Jetstream\Http\Controllers\Livewire\TeamController;
use Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController;
use Laravel\Jetstream\Jetstream;

Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    Route::group(['middleware' => ['auth', 'verified']], function () {
        // User & Profile...
        Route::get('/user/profile', [UserProfileController::class, 'show'])
            ->name('profile.show');

        // API...
        if (Jetstream::hasApiFeatures()) {
            Route::get('/user/api-tokens', [ApiTokenManagerController::class, 'index'])->name('api-tokens.index');
            Route::post('/user/api-tokens', [ApiTokenManagerController::class, 'store'])->name('api-tokens.store');
            Route::delete('/user/api-tokens/{personalAccessToken}', [ApiTokenManagerController::class, 'destroy'])->name('api-tokens.destroy');
            Route::get('/user/api-tokens/{personalAccessToken}', [ApiTokenManagerController::class, 'updateForm'])->name('api-tokens.updateForm');
            Route::patch('/user/api-tokens/{personalAccessToken}', [ApiTokenManagerController::class, 'update'])->name('api-tokens.update');
        }

        // Teams...
        if (Jetstream::hasTeamFeatures()) {
            Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
            Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
            Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
        }
    });
});
