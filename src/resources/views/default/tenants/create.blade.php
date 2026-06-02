@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('tenants.add') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('tenants.add') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{route('tenants.store')}}">
        @csrf

        <!-- Name -->
            <div class="field">
                <label for="name" class="label">{{ __('attributes.name') }}</label>
                <div class="control">
                    <input id="name" name="name" type="text"
                           class="input"
                           value="{{old('name')}}"
                           maxlength="22"
                           required>
                    <small>{{ __('tenants.name_small') }}</small>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{ route('tenants.index') }} }}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>

@endsection
