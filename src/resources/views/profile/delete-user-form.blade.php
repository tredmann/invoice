<div>
    <h2 class="title is-size-6 is-uppercase">{{ __('Delete Account') }}</h2>
    <p class="mb-4 has-text-grey">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

    <button type="button" class="button is-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
        {{ __('Delete Account') }}
    </button>

    <div class="modal" :class="{'is-active': $wire.confirmingUserDeletion}" x-data>
        <div class="modal-background" wire:click="$set('confirmingUserDeletion', false)"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ __('Delete Account') }}</p>
            </header>
            <section class="modal-card-body">
                <p class="mb-4">{{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                <div class="field">
                    <div class="control">
                        <input type="password" class="input" placeholder="{{ __('Password') }}"
                               wire:model="password"
                               wire:keydown.enter="deleteUser">
                    </div>
                    @error('password') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </section>
            <footer class="modal-card-foot is-justify-content-flex-end">
                <button type="button" class="button" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </button>
                <button type="button" class="button is-danger ml-2" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Delete Account') }}
                </button>
            </footer>
        </div>
    </div>
</div>
