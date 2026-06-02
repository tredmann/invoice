@if(count($mailReceivers) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.email') }}</th>
                <th class="fit">{{ __('attributes.gender') }}</th>
                <th class="fit">{{ __('attributes.first_name') }}</th>
                <th>{{ __('attributes.last_name') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody>
            @foreach($mailReceivers as $mailReceiver)
                <tr>
                    <td class="fit">
                        {{$mailReceiver->email}}
                    </td>
                    <td class="fit">
                        {{$mailReceiver->gender}}
                    </td>
                    <td class="fit">
                        {{$mailReceiver->first_name}}
                    </td>
                    <td>
                        {{$mailReceiver->last_name}}
                    </td>
                    <td class="fit">

                        @component('default.customers.components.customersMailReceiversTriggerMenu', ['mailReceiver' => $mailReceiver, 'tenant' => $tenant])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
