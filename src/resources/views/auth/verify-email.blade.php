@extends('auth.layout.app')

@section('content')
    <div id="auth_form" class="box p-0">
        <section id="form_title" class="p-4">
            <h1 class="title is-size-6">{{ __('Verify Email') }}</h1>
        </section>
        <section id="auth_form_content" class="p-4">
            <p class="mb-4 has-text-grey">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>

            @if (session('status') == 'verification-link-sent')
                <p class="mb-4 has-text-success has-text-weight-medium">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </p>
            @endif

            <div class="is-flex is-justify-content-space-between is-align-items-center mt-5">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="button is-primary">{{ __('Resend Verification Email') }}</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button is-text">{{ __('Logout') }}</button>
                </form>
            </div>
        </section>
    </div>
@endsection
