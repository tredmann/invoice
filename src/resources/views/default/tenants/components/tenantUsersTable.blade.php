<section id="content_section" class="mt-5 box pb-1 p-0">
    <div id="content_head">
        <h1>{{ __('tenants.users') }}</h1>
    </div>

    @if(count($tenantUsers) === 0)
        <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
    @else
        <div class="table-container">
            <table class="table is-fullwidth is-hoverable">
                <thead>
                <tr>
                    <th class="fit">{{ __('attributes.name') }}</th>
                    <th>{{ __('attributes.email') }}</th>
                    <th class="fit">{{ __('attributes.role') }}</th>
                    <th class="fit">{{ __('attributes.added') }}</th>
                    <th class="fit"><!-- TRIGGER MENU --></th>
                </tr>
                </thead>
                <tbody>
                @foreach($tenantUsers as $tenantUser)
                    <tr>
                        <td class="fit">
                            {{ $tenantUser->name }}
                        </td>
                        <td>
                            {{ $tenantUser->email }}
                        </td>
                        <td class="fit">
                            {{ $tenant->owner_id === $tenantUser->id ? __('tenants.owner') : __('tenants.staff') }}
                        </td>
                        <td class="fit">
                            {{$tenantUser->tenants()->find($tenant)->pivot->created_at ? $tenantUser->tenants()->find($tenant)->pivot->created_at->format('d.m.Y H:m:s') : null}}
                        </td>
                        <td class="fit">

                            @component('default.tenants.components.tenantUsersTriggerMenu', ['tenantUser' => $tenantUser, 'tenant' => $tenant])
                            @endcomponent

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
