<div class="table-container">
    <table class="table is-fullwidth is-hoverable">
        <tbody>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.customer_no') }}
            </td>
            <td>
                {{$customer->customer_no}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.company') }}
            </td>
            <td>
                {{$customer->company}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.street') }}
            </td>
            <td>
                {{$customer->street}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.postal') }}
            </td>
            <td>
                {{$customer->postal}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.city') }}
            </td>
            <td>
                {{$customer->city}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.created_at') }}
            </td>
            <td>
                {{$customer->created_at->diffInDays(now()) < 1 ? $customer->created_at->diffForHumans() : $customer->created_at->format('d.m.Y')}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.updated_at') }}
            </td>
            <td>
                {{$customer->created_at->diffInDays(now()) < 1 ? $customer->created_at->diffForHumans() : $customer->created_at->format('d.m.Y')}}
            </td>
        </tr>
        </tbody>
    </table>
</div>
