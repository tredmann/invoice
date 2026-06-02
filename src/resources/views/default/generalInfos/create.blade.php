@extends('layouts.app')

@section('content')
    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('generalInfos.add') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('generalInfos.add') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('generalInfos.store')}}">
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

            <!-- Owner -->
            <div class="field">
                <label for="owner" class="label">{{ __('attributes.owner') }}</label>
                <div class="control">
                    <input id="owner" name="owner" type="text"
                           class="input"
                           value="{{old('owner')}}"
                           required>
                </div>
            </div>

            <!-- Additional Address -->
            <div class="field">
                <label for="additional_address" class="label">{{ __('attributes.additional_address') }}</label>
                <div class="control">
                    <input id="additional_address" name="additional_address" type="text"
                           class="input"
                           value="{{old('additional_address')}}"
                           placeholder="{{ __('translate.optional') }}">
                </div>
            </div>

            <!-- Street -->
            <div class="field">
                <label for="street" class="label">{{ __('attributes.street') }}</label>
                <div class="control">
                    <input id="street" name="street" type="text"
                           class="input"
                           value="{{old('street')}}"
                           required>
                </div>
            </div>

            <!-- Postal -->
            <div class="field">
                <label for="postal" class="label">{{ __('attributes.postal') }}</label>
                <div class="control">
                    <input id="postal" name="postal" type="text"
                           class="input"
                           value="{{old('postal')}}"
                           required>
                </div>
            </div>

            <!-- City -->
            <div class="field">
                <label for="city" class="label">{{ __('attributes.city') }}</label>
                <div class="control">
                    <input id="city" name="city" type="text"
                           class="input"
                           value="{{old('city')}}"
                           required>
                </div>
            </div>

            <!-- Country -->
            <div class="field">
                <label for="country" class="label">{{ __('attributes.country') }}</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select id="country" name="country" required>
                            @foreach(\App\Models\Tenant\GeneralInfo::COUNTRIES as $country)
                                <option @if(old('country') === $country) selected @endif>{{$country}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fax -->
            <div class="field">
                <label for="fax" class="label">{{ __('attributes.fax') }}</label>
                <div class="control">
                    <input id="fax" name="fax" type="text"
                           class="input"
                           value="{{old('fax')}}"
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

            <!-- Homepage -->
            <div class="field">
                <label for="homepage" class="label">{{ __('attributes.homepage') }}</label>
                <div class="control">
                    <input id="homepage" name="homepage" type="url"
                           class="input"
                           value="{{old('homepage')}}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('generalInfos.index')}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>
@endsection
