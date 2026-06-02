<div>
    <h2 class="title is-size-6 is-uppercase">{{ __('Two Factor Authentication') }}</h2>
    <p class="mb-4 has-text-grey">{{ __('Add additional security to your account using two factor authentication.') }}</p>

    <h3 class="title is-size-6 mb-3">
        @if ($this->enabled)
            {{ __('You have enabled two factor authentication.') }}
        @else
            {{ __('You have not enabled two factor authentication.') }}
        @endif
    </h3>

    <p class="mb-4 has-text-grey">{{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}</p>

    @if ($this->enabled)
        @if ($showingQrCode)
            <p class="mb-3 has-text-weight-semibold">{{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}</p>
            <div class="mb-4">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>
        @endif

        @if ($showingRecoveryCodes)
            <p class="mb-3 has-text-weight-semibold">{{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}</p>
            <div class="p-3 mb-4 has-background-grey-lighter is-family-monospace is-size-7" style="border-radius: 4px;">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div>{{ $code }}</div>
                @endforeach
            </div>
        @endif
    @endif

    <div class="field is-grouped mt-4">
        @if (! $this->enabled)
            <div class="control">
                <button type="button" class="button is-primary" wire:click="enableTwoFactorAuthentication" wire:loading.attr="disabled">
                    {{ __('Enable') }}
                </button>
            </div>
        @else
            @if ($showingRecoveryCodes)
                <div class="control">
                    <button type="button" class="button" wire:click="regenerateRecoveryCodes">
                        {{ __('Regenerate Recovery Codes') }}
                    </button>
                </div>
            @else
                <div class="control">
                    <button type="button" class="button" wire:click="showRecoveryCodes">
                        {{ __('Show Recovery Codes') }}
                    </button>
                </div>
            @endif

            <div class="control">
                <button type="button" class="button is-danger" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                    {{ __('Disable') }}
                </button>
            </div>
        @endif
    </div>
</div>
