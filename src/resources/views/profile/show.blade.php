@extends('layouts.app')

@section('content')
    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('Profile') }}</h1>
    </section>

    <section id="content_section" class="mt-5">
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updateProfileInformation()))
            <div class="box mb-5">
                @livewire('profile.update-profile-information-form')
            </div>
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="box mb-5">
                @livewire('profile.update-password-form')
            </div>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="box mb-5">
                @livewire('profile.two-factor-authentication-form')
            </div>
        @endif

        <div class="box mb-5">
            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        <div class="box mb-5">
            @livewire('profile.delete-user-form')
        </div>
    </section>
@endsection
