<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::registerView(function () {
            return view('auth.register', ['title' => __('auth.titles.register')]);
        });

        Fortify::loginView(function () {
            return view('auth.login', ['title' => __('auth.titles.login')]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password', ['title' => __('auth.titles.forgot_password')]);
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['title' => __('auth.titles.reset_password'), 'request' => $request]);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }
}
