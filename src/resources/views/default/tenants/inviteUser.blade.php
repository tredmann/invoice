@extends('layouts.app')

@section('content')
    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('tenants.invite_user') }}</h1>
    </section>


    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('tenants.invite_user') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{ route('tenants.invite-user', ['tenant' => $tenant]) }}">
        @csrf
        @method('PATCH')

        <!-- Tenant Name -->
            <div class="field">
                <label for="tenant_name" class="label">{{ __('tenants.tenant') }}</label>
                <div class="control">
                    <input id="tenant_name" name="tenant_name" type="text"
                           class="input"
                           value="{{$tenant->name}}"
                           disabled>
                </div>
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email" class="label">{{ __('attributes.email') }}</label>
                <div class="control">
                    <input id="email" name="email" type="text"
                           class="input"
                           value="{{old('email')}}"
                           required>
                    <small>{{ __('tenants.add_user_email_small') }}</small>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{ route('tenants.users', ['tenant' => $tenant]) }}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>

@endsection

