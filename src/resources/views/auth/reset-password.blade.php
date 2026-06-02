@extends('auth.layout.app')

@section('content')
    <form id="auth_form" class="box p-0" method="POST" action="{{ route('password.update') }}">
        @csrf
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('auth.titles.reset_password') }}</h1>
        </section>

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                    <input type="password" id="password" name="password"
                           class="input @if($errors->has('password')) is-danger @endif" required>
                </div>
                @if($errors->has('password'))
                    <p class="help is-danger">{{ $errors->first('password') }}</p>
                @else
                    <p class="help"> {{ __('auth.help.password') }} </p>
                @endif
            </div>

            <!-- Password Confirmation-->
            <div class="field">
                <label for="password_confirmation" class="label"> {{ __('auth.labels.password_confirm') }} </label>
                <div class="control">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="input"
                           required>
                </div>
            </div>
        </section>

        <section id="button_section" class="p-4">
            <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                <!-- Cancel -->
                <a href="{{ route('login') }}"
                   class="button is-outlined is-radiusless">{{ __('auth.buttons.cancel') }}</a>

                <!-- Register -->
                <button type="submit"
                        class="button is-danger is-radiusless">{{ __('auth.buttons.reset_password') }}</button>
            </div>
        </section>
    </form>
@endsection

