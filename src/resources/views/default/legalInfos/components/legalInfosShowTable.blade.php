@if(!$legalInfo)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <table class="table is-fullwidth is-hoverable">
        <tbody>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.registry_court') }}
            </td>
            <td>
                {{$legalInfo->registry_court}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.registry_no') }}
            </td>
            <td>
                {{$legalInfo->registry_no}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.registry_no') }}
            </td>
            <td>
                {{$legalInfo->company_owner}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.tax_no') }}
            </td>
            <td>
                {{$legalInfo->tax_no}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.vat_no') }}
            </td>
            <td>
                {{$legalInfo->vat_no}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.swift_bic') }}
            </td>
            <td>
                {{$legalInfo->swift_bic}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.iban') }}
            </td>
            <td>
                {{$legalInfo->iban}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.bank_institute') }}
            </td>
            <td>
                {{$legalInfo->bank_institute}}
            </td>
        </tr>
        </tbody>
    </table>
@endif
