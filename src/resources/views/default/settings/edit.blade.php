@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('settings.edit') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('settings.edit') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('settings.update', ['setting' => $setting])}}">
        @csrf
        @method('PATCH')

        <!-- Key -->
            <div class="field">
                <label for="key" class="label">{{ __('attributes.key') }}</label>
                <div class="control">
                    <input id="key" name="key" type="text"
                           class="input"
                           value="{{old('key') ?? $setting->key}}"
                           required>
                </div>
            </div>

            <!-- Value -->
            <div class="field">
                <label for="value" class="label">{{ __('attributes.value') }}</label>
                <div class="control">
                    <input id="value" name="value" type="text"
                           class="input"
                           value="{{old('value') ?? $setting->value}}"
                           required>
                </div>
            </div>

            <!-- Type -->
            <div class="field">
                <label for="type" class="label">{{ __('attributes.type') }}</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select id="type" name="type" required>
                            @foreach(\App\Models\Setting::VALUE_TYPES as $type)
                                <option
                                    @if(old('type') ? old('type') === $type : $type === $setting->type) selected @endif>{{$type}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('settings.index')}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>
@endsection
