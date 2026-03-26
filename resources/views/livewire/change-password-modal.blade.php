<div
    x-data="{ isOpen: @entangle('isOpen').live }"
    x-on:modal-opened.window="isOpen = true"
>
    <x-filament::modal
        id="change-password-modal"
        x-model="isOpen"
        width="md"
        @close="isOpen = false; $wire.closeModal()"
    >
        <x-slot name="heading">
            Change Password
        </x-slot>

        <x-slot name="description">
            Update your account password. Make sure to use a strong password.
        </x-slot>

        <form wire:submit="updatePassword">
            {{ $this->form }}

            <div class="flex justify-end gap-3 mt-6">
                <x-filament::button
                    color="gray"
                    type="button"
                    @click="isOpen = false; $wire.closeModal()"
                >
                    Cancel
                </x-filament::button>

                <x-filament::button type="submit">
                    Update Password
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</div>
