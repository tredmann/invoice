@extends('layouts.app')

@section('content')

    {{ Breadcrumbs::render() }}

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('api-token-manager.update_api_token') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">

        <form id="content_form" class="p-4" method="POST" action="{{route('api-tokens.update', ['personalAccessToken' => $apiToken])}}">
        @csrf
        @method('PATCH')

        <!-- Name -->
        <div class="field">
            <label for="name" class="label">{{ __('api-token-manager.token_name') }}</label>
            <div class="control">
                <input id="name" name="name" type="text"
                       class="input"
                       value="{{old('name') ?? $apiToken->name}}"
                       required>
            </div>
        </div>

        <!-- Permissions -->
            <div class="field">
                <label for="permissions" class="label">{{ __('api-token-manager.permissions') }}</label>
                <div id="permissions" class="field is-grouped">
                    @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                        <p class="control">
                            <label class="checkbox">
                                <input id="permission-{{$permission}}" type="checkbox" name="permissions[]" value="{{$permission}}" @if(old('permissions') ? in_array($permission, old('permissions'), true) : in_array($permission, $apiToken->abilities, true) ) checked @endif/>
                                {{ __("api-token-manager.$permission") }}
                            </label>
                        </p>
                    @endforeach
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">

                    <!-- Done -->
                    <button name="submit"
                            value="done"
                            type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}
                    </button>

                </div>
            </section>

        </form>
    </section>
@endsection
