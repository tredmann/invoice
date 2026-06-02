@extends('auth.layout.app')

@section('content')
    <form id="auth_form" class="box p-0" method="POST" action="{{ route('register') }}">
        @csrf
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('auth.titles.register') }}</h1>
        </section>
        <section id="auth_form_content" class="p-4">
            <!-- First name -->
            <div class="field">
                <label for="first_name" class="label"> {{ __('auth.labels.first_name') }} </label>
                <div class="control">
                    <input id="first_name" name="first_name" type="text"
                           class="input  @if($errors->has('first_name')) is-danger @endif" required>
                </div>
                @if($errors->has('first_name'))
                    <p class="help is-danger">{{ $errors->first('first_name') }}</p>
                @endif
            </div>

            <!-- Last name -->
            <div class="field">
                <label for="last_name" class="label"> {{ __('auth.labels.last_name') }} </label>
                <div class="control">
                    <input id="last_name" name="last_name" type="text"
                           class="input @if($errors->has('last_name')) is-danger @endif" required>
                </div>
                @if($errors->has('last_name'))
                    <p class="help is-danger">{{ $errors->first('last_name') }}</p>
                @endif
            </div>

            <!-- E-Mail -->
            <div class="field">
                <label for="email" class="label"> {{ __('auth.labels.email') }} </label>
                <div class="control">
                    <input id="email" name="email" type="email"
                           class="input @if($errors->has('email')) is-danger @endif" required>
                </div>
                @if($errors->has('email'))
                    <p class="help is-danger">{{ $errors->first('email') }}</p>
                @endif
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
                <a href="{{ route('login') }}" class="button is-outlined is-radiusless">{{ __('auth.buttons.cancel') }}</a>

                <!-- Register -->
                <button type="submit" class="button is-danger is-radiusless">{{ __('auth.buttons.register') }}</button>
            </div>
        </section>
    </form>
@endsection
