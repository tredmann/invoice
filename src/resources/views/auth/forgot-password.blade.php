@extends('auth.layout.app')

@section('content')
    <form id="auth_form" class="box p-0" method="POST" action="{{ route('password.email') }}">
        @csrf
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('auth.titles.forgot_password') }}</h1>
            <h2 class="subtitle is-size-6">
                {{ __('auth.subtitles.forgot_password') }}
            </h2>
        </section>

        <section id="auth_form_content" class="p-4">
        @if ($errors || session()->has('status') ||  session()->has('error'))
            @include('auth.layout.notification')
        @endif
            <!-- E-Mail -->
            <div class="field">
                <label for="email" class="label"> {{ __('auth.labels.email') }} </label>
                <div class="control">
                    <input id="email" name="email" type="email" class="input" required>
                </div>
            </div>
        </section>

        <section id="button_section" class="p-4">
            <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                <!-- Cancel -->
                <a href="{{ route('login') }}" class="button is-outlined is-radiusless">{{ __('auth.buttons.cancel') }}</a>

                <!-- Register -->
                <button type="submit" class="button is-danger is-radiusless">{{ __('auth.buttons.send_reset_link') }}</button>
            </div>
        </section>
    </form>
@endsection
