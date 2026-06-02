<div>
    <h2 class="title is-size-6 is-uppercase">{{ __('Profile Information') }}</h2>
    <p class="mb-4 has-text-grey">{{ __('Update your account\'s profile information and email address.') }}</p>

    <form wire:submit="updateProfileInformation">
        <div class="field">
            <label for="name" class="label">{{ __('Name') }}</label>
            <div class="control">
                <input id="name" type="text" class="input" wire:model="state.name" autocomplete="name">
            </div>
            @error('state.name') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="email" class="label">{{ __('Email') }}</label>
            <div class="control">
                <input id="email" type="email" class="input" wire:model="state.email">
            </div>
            @error('state.email') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field is-grouped is-align-items-center mt-4">
            <div class="control">
                <button type="submit" class="button is-primary" wire:loading.attr="disabled">{{ __('Save') }}</button>
            </div>
            <p class="control has-text-success" x-data="{shown: false}" x-on:saved.window="shown = true; setTimeout(() => shown = false, 2500)" x-show="shown" x-cloak>{{ __('Saved.') }}</p>
        </div>
    </form>
</div>
