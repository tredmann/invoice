@if(count($tenants) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.status') }}</th>
                <th class="fit">{{ __('attributes.name') }}</th>
                <th >{{ __('attributes.owner') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($tenants as $tenant)
                <tr>
                    <td>
                        @if(count($tenant->setupErrors()) > 0)
                            <div class="tag is-danger" title="{{ implode(', ', $tenant->setupErrors())  }}">
                               <span class="icon">
                                      <i class="fas fa-exclamation-triangle"></i>
                                  </span>&nbsp; Setup Errors
                            </div>
                        @else
                            <div class="tag is-primary" >
                               <span class="icon">
                                      <i class="fas fa-check"></i>
                                  </span>&nbsp; OK
                            </div>
                        @endif
                    </td>
                    <td class="fit">
                        <strong>{{ $tenant->name }}</strong>

                    </td>
                    <td>
                        {{ $tenant->owner->name }}
                    </td>

                    <td class="fit" onclick="event.stopPropagation();">

                    @component('default.tenants.components.tenantsTriggerMenu', ['tenant' => $tenant])
                    @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
