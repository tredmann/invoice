<div class="table-container">
    <table class="table is-fullwidth is-hoverable">
        <tbody>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.slug') }}
            </td>
            <td>
                {{$tenant->slug}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.name') }}
            </td>
            <td>
                {{$tenant->name}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.owner') }}
            </td>
            <td>
                {{$tenant->owner->email}}
            </td>
        </tr>
        </tbody>
    </table>
</div>
