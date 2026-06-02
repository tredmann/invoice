@if(count($users) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.name') }}</th>
                <th class="fit">{{ __('attributes.email') }}</th>
                <th>{{ __('attributes.role') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="fit">
                        {{$user->name}}
                    </td>
                    <td class="fit">
                        {{$user->email}}
                    </td>
                    <td>
                        {{$user->is_admin === true ? 'Admin' : 'Nutzer'}}
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">

                        @if(Auth::id() !== $user->id)
                            @component('admin.user-panel.components.usersTriggerMenu', ['user' => $user])
                            @endcomponent
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
