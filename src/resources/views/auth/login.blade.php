@extends('auth.layout.app')

@section('content')
    <form id="auth_form" class="box p-0" method="POST" action="{{ route('login') }}">
        @csrf
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('auth.titles.login') }}</h1>
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

            <!-- Password -->
            <div class="field">
                <label for="password" class="label"> {{ __('auth.labels.password') }} </label>
                <div class="control">
                    <input type="password" id="password" name="password" class="input" required>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="mt-5 field">
                <div class="control">
                    <input id="remember_checkbox" type="checkbox" name="remember"
                           class="is-checkradio is-danger is-small">
                    <label for="remember_checkbox" onclick="toggleCheckbox()">
                        <span>{{ __('auth.labels.remember_me') }}</span>
                    </label>
                </div>
            </div>

            <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('auth.labels.password_forgot') }}
                    </a>
                @endif
                <button type="submit" class="button is-danger is-radiusless">{{ __('auth.buttons.login') }}</button>
            </div>
        </section>
    </form>
@endsection
@section('scripts@Footer')
    <script type="application/javascript">
        function toggleCheckbox() {
            $('#remember_checkbox').toggleClass('has-background-color')
        }
    </script>
@endsection
