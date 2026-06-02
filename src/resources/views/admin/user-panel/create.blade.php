@extends('layouts.app')

@section('content')

    {{ Breadcrumbs::render() }}

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('admin/user-panel.add') }}</h1>
    </section>


    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('admin/user-panel.add') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{route('admin.user-panel.store')}}">
        @csrf

        <!-- Name -->
            <div class="field">
                <label for="name" class="label">{{ __('attributes.name') }}</label>
                <div class="control">
                    <input id="name" name="name" type="text"
                           class="input"
                           value="{{old('name')}}"
                           required>
                </div>
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email" class="label">{{ __('attributes.email') }}</label>
                <div class="control">
                    <input id="email" name="email" type="email"
                           class="input"
                           value="{{old('email')}}"
                           required>
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label for="password" class="label">{{ __('attributes.password') }}</label>
                <div class="control">
                    <input id="password" name="password" type="password"
                           class="input"
                           value="{{old('password')}}"
                           required>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div class="field">
                <label for="password_confirmation" class="label">{{ __('attributes.password_confirmation') }}</label>
                <div class="control">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="input"
                           value="{{old('password_confirmation')}}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{route('admin.user-panel.index')}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>
@endsection
