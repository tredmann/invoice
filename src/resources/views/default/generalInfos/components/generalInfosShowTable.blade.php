@if(!$generalInfo)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <table class="table is-fullwidth is-hoverable">
        <tbody>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.name') }}
            </td>
            <td>
                {{$generalInfo->name}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.owner') }}
            </td>
            <td>
                {{$generalInfo->owner}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.additional_address') }}
            </td>
            <td>
                {{$generalInfo->additional_address}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.street') }}
            </td>
            <td>
                {{$generalInfo->street}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.postal') }}
            </td>
            <td>
                {{$generalInfo->postal}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.city') }}
            </td>
            <td>
                {{$generalInfo->city}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.country') }}
            </td>
            <td>
                {{$generalInfo->country}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.fax') }}
            </td>
            <td>
                {{$generalInfo->fax}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.email') }}
            </td>
            <td>
                {{$generalInfo->email}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.homepage') }}
            </td>
            <td>
                {{$generalInfo->homepage}}
            </td>
        </tr>
        </tbody>
    </table>
@endif

