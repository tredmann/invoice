<section id="content_section" class="mt-5 box pb-1 p-0">
    <div id="content_head">
        <h1>{{ __('api-token-manager.create_api_token') }}</h1>
        <h2>{{ __('api-token-manager.create_description') }}</h2>
    </div>

    <form id="content_form" class="p-4" method="POST" action="{{route('api-tokens.store')}}">
        @csrf

        <!-- Name -->
        <div class="field">
            <label for="name" class="label">{{ __('api-token-manager.token_name') }}</label>
            <div class="control">
                <input id="name" name="name" type="text"
                       class="input"
                       value="{{old('name')}}"
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
                            <input id="permission-{{$permission}}" type="checkbox" name="permissions[]" value="{{$permission}}"/>
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

<section id="content_section" class="mt-5 box pb-1 p-0">
    <div id="content_head">
        <h1>{{ __('api-token-manager.manage_api_tokens') }}</h1>
        <h2>{{ __('api-token-manager.manage_description') }}</h2>
    </div>

    @if(count($apiTokens) === 0)
        <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
    @else
        <div class="table-container">
            <table class="table is-fullwidth is-hoverable">
                <tbody>
                @foreach($apiTokens->sortBy('name') as $apiToken)
                    <tr>
                        <td class="fit">
                            {{ $apiToken->name }}
                        </td>
                        <td>
                            @if($apiToken->last_used_at)
                                {{ __('api-token-manager.last_used') }} {{$apiToken->last_used_at->diffForHumans()}}
                            @endif
                        </td>
                        <td>
                            {{ __('api-token-manager.permissions') }}:
                            @foreach($apiToken->abilities as $ability)
                                {{ __('api-token-manager.'.$ability) }}
                            @endforeach
                        </td>

                        <td class="fit">

                            @component('api-token-manager.components.managerTriggerMenu', ['apiToken' => $apiToken])
                            @endcomponent

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
