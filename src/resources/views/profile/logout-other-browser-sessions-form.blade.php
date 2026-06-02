<div>
    <h2 class="title is-size-6 is-uppercase">{{ __('Browser Sessions') }}</h2>
    <p class="mb-4 has-text-grey">{{ __('Manage and logout your active sessions on other browsers and devices.') }}</p>
    <p class="mb-4">{{ __('If necessary, you may logout of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}</p>

    @if (count($this->sessions) > 0)
        <div class="mb-5">
            @foreach ($this->sessions as $session)
                <div class="is-flex is-align-items-center mb-3">
                    <span class="icon is-medium has-text-grey">
                        @if ($session->agent->isDesktop())
                            <i class="fas fa-desktop"></i>
                        @else
                            <i class="fas fa-mobile-alt"></i>
                        @endif
                    </span>
                    <div class="ml-3">
                        <div class="is-size-7">{{ $session->agent->platform() }} - {{ $session->agent->browser() }}</div>
                        <div class="is-size-7 has-text-grey">
                            {{ $session->ip_address }},
                            @if ($session->is_current_device)
                                <span class="has-text-success has-text-weight-semibold">{{ __('This device') }}</span>
                            @else
                                {{ __('Last active') }} {{ $session->last_active }}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="is-flex is-align-items-center">
        <button type="button" class="button is-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
            {{ __('Logout Other Browser Sessions') }}
        </button>
        <p class="ml-3 has-text-success" x-data="{shown: false}" x-on:loggedOut.window="shown = true; setTimeout(() => shown = false, 2500)" x-show="shown" x-cloak>{{ __('Done.') }}</p>
    </div>

    <div class="modal" :class="{'is-active': $wire.confirmingLogout}" x-data>
        <div class="modal-background" wire:click="$set('confirmingLogout', false)"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ __('Logout Other Browser Sessions') }}</p>
            </header>
            <section class="modal-card-body">
                <p class="mb-4">{{ __('Please enter your password to confirm you would like to logout of your other browser sessions across all of your devices.') }}</p>
                <div class="field">
                    <div class="control">
                        <input type="password" class="input" placeholder="{{ __('Password') }}"
                               wire:model="password"
                               wire:keydown.enter="logoutOtherBrowserSessions">
                    </div>
                    @error('password') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </section>
            <footer class="modal-card-foot is-justify-content-flex-end">
                <button type="button" class="button" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </button>
                <button type="button" class="button is-primary ml-2" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                    {{ __('Logout Other Browser Sessions') }}
                </button>
            </footer>
        </div>
    </div>
</div>
