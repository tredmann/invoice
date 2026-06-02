@if(count($customers) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.customer_no') }}</th>
                <th class="fit">{{ __('attributes.company') }}</th>
                <th class>{{ __('attributes.name') }}</th>
                <th><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($customers as $customer)
                <tr>
                    <td class="fit">
                        {{$customer->customer_no}}
                    </td>
                    <td class="fit">
                        {{$customer->company}}
                    </td>
                    <td>
                        {{$customer->name}}
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">

                        @component('default.customers.components.customersIndexTriggerMenu', ['customer' => $customer, 'tenant' => $tenant])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
@endif
