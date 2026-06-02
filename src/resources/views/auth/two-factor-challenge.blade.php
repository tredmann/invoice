@extends('auth.layout.app')

@section('content')
    <form id="auth_form" class="box p-0" method="POST" action="/two-factor-challenge" x-data="{ recovery: false }">
        @csrf
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('Two Factor Authentication') }}</h1>
        </section>
        <section id="auth_form_content" class="p-4">
            @if ($errors->any() || session()->has('status') || session()->has('error'))
                @include('auth.layout.notification')
            @endif

            <p class="mb-4 has-text-grey" x-show="! recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>

            <p class="mb-4 has-text-grey" x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </p>

            <div class="field" x-show="! recovery">
                <label for="code" class="label">{{ __('Code') }}</label>
                <div class="control">
                    <input id="code" type="text" inputmode="numeric" name="code" class="input" autofocus x-ref="code" autocomplete="one-time-code">
                </div>
            </div>

            <div class="field" x-show="recovery">
                <label for="recovery_code" class="label">{{ __('Recovery Code') }}</label>
                <div class="control">
                    <input id="recovery_code" type="text" name="recovery_code" class="input" x-ref="recovery_code" autocomplete="one-time-code">
                </div>
            </div>

            <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                <a href="#" x-show="! recovery"
                   x-on:click.prevent="recovery = true; $nextTick(() => $refs.recovery_code.focus())">
                    {{ __('Use a recovery code') }}
                </a>
                <a href="#" x-show="recovery"
                   x-on:click.prevent="recovery = false; $nextTick(() => $refs.code.focus())">
                    {{ __('Use an authentication code') }}
                </a>
                <button type="submit" class="button is-danger is-radiusless">{{ __('Login') }}</button>
            </div>
        </section>
    </form>
@endsection
