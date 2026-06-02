<div>
    <h2 class="title is-size-6 is-uppercase">{{ __('Update Password') }}</h2>
    <p class="mb-4 has-text-grey">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

    <form wire:submit="updatePassword">
        <div class="field">
            <label for="current_password" class="label">{{ __('Current Password') }}</label>
            <div class="control">
                <input id="current_password" type="password" class="input" wire:model="state.current_password" autocomplete="current-password">
            </div>
            @error('state.current_password') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password" class="label">{{ __('New Password') }}</label>
            <div class="control">
                <input id="password" type="password" class="input" wire:model="state.password" autocomplete="new-password">
            </div>
            @error('state.password') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation" class="label">{{ __('Confirm Password') }}</label>
            <div class="control">
                <input id="password_confirmation" type="password" class="input" wire:model="state.password_confirmation" autocomplete="new-password">
            </div>
            @error('state.password_confirmation') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field is-grouped is-align-items-center mt-4">
            <div class="control">
                <button type="submit" class="button is-primary">{{ __('Save') }}</button>
            </div>
            <p class="control has-text-success" x-data="{shown: false}" x-on:saved.window="shown = true; setTimeout(() => shown = false, 2500)" x-show="shown" x-cloak>{{ __('Saved.') }}</p>
        </div>
    </form>
</div>
