<?php

declare(strict_types=1);

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
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::registerView(fn () => view('auth.register', ['title' => __('auth.titles.register')]));

        Fortify::loginView(fn () => view('auth.login', ['title' => __('auth.titles.login')]));

        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password', ['title' => __('auth.titles.forgot_password')]));

        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['title' => __('auth.titles.reset_password'), 'request' => $request]));

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }
}
