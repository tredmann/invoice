@if(count($settings) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.key') }}</th>
                <th class="fit">{{ __('attributes.value') }}</th>
                <th>{{ __('attributes.type') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td class="fit">
                        {{$setting->key}}
                    </td>
                    <td class="fit">
                        @if($setting->type !== \App\Models\Setting::VALUE_SECRET)
                            {{$setting->value}}
                        @else
                            {{Str::repeat('*', random_int(2,9))}}
                        @endif
                    </td>
                    <td>
                        {{$setting->type}}
                    </td>
                    <td class="fit">

                        @component('default.settings.components.settingsTriggerMenu', ['setting' => $setting, 'tenant' => $tenant])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
