<div
    x-data="{ isOpen: @entangle('isOpen').live, showLightbox: false }"
    x-on:modal-opened.window="isOpen = true"
>
    <x-filament::modal
        id="pest-disease-approval-modal"
        x-model="isOpen"
        width="3xl"
        @close="isOpen = false; $wire.closeModal()"
    >
        <x-slot name="heading">
            @if($record)
                Detection: {{ $record->pest }}
            @else
                Pest/Disease Approval
            @endif
        </x-slot>

        @if($record)
            <div class="space-y-4 dark:bg-custom-color-darkmode">
                {{-- Main Content: Image Left, Details Right --}}
                <div class="flex flex-col md:flex-row gap-6 pt-2">
                    {{-- Left: Image --}}
                    <div class="flex-shrink-0">
                        @if($record->image_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($record->image_path) }}"
                                alt="Detection Image"
                                class="w-48 h-48 rounded-lg shadow-lg object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                @click="showLightbox = true"
                                title="Click to enlarge"
                            />
                        @else
                            <div class="flex items-center justify-center w-48 h-48 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <span class="text-gray-400 text-sm">No image</span>
                            </div>
                        @endif

                        {{-- Status Badge below image --}}
                        <div class="mt-3 flex justify-center">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'disapproved' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                ];
                                $statusColor = $statusColors[$record->validation_status] ?? $statusColors['pending'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                @if($record->validation_status === 'approved')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @elseif($record->validation_status === 'disapproved')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                @endif
                                {{ ucfirst($record->validation_status) }}
                            </span>
                        </div>

                        @if($record->image_path)
                            <p class="text-xs text-gray-400 text-center mt-1">Click image to enlarge</p>
                        @endif
                    </div>

                    {{-- Right: Details Grid --}}
                    <div class="flex-1 grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Pest/Disease</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->pest }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Type</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($record->type ?? 'N/A') }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Severity</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($record->severity) }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Area</h4>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->area }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Description</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->description ?? 'No description' }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Detected</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->date_detected ? \Carbon\Carbon::parse($record->date_detected)->format('M d, Y') : 'N/A' }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg col-span-2">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Coordinates</h4>
                            <p class="mt-1 text-xs text-gray-900 dark:text-white">
                                {{ number_format($record->latitude, 4) }}, {{ number_format($record->longitude, 4) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Expert Comments (if disapproved) --}}
                @if($record->validation_status === 'disapproved' && $record->expert_comments)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 rounded-lg">
                        <h4 class="text-xs font-medium text-red-800 dark:text-red-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                            </svg>
                            Expert Comments
                        </h4>
                        <p class="text-sm text-red-700 dark:text-red-200 mt-2">{{ $record->expert_comments }}</p>
                    </div>
                @endif

                {{-- Lightbox Overlay for Image --}}
                @if($record->image_path)
                    <div
                        x-show="showLightbox"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="showLightbox = false"
                        @keydown.escape.window="showLightbox = false"
                        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
                        style="display: none;"
                    >
                        {{-- Close Button --}}
                        <button
                            @click="showLightbox = false"
                            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors"
                        >
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        {{-- Full Size Image --}}
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($record->image_path) }}"
                            alt="Detection Image - Full Size"
                            class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                            @click.stop
                        />

                        {{-- Image Info --}}
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-lg text-sm">
                            {{ $record->pest }} - {{ ucfirst($record->type ?? 'N/A') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Approval Form --}}
            @if($record->validation_status === 'pending')
                <div class="mt-6 space-y-4">
                    {{ $this->form }}

                    <div class="flex justify-end gap-3 mt-6">
                        <x-filament::button
                            color="gray"
                            type="button"
                            @click="isOpen = false; $wire.closeModal()"
                        >
                            Close
                        </x-filament::button>

                        <x-filament::button
                            color="success"
                            type="button"
                            wire:click="approve"
                        >
                            Approve
                        </x-filament::button>

                        <x-filament::button
                            color="danger"
                            type="button"
                            wire:click="disapprove"
                        >
                            Disapprove
                        </x-filament::button>
                    </div>
                </div>
            @else
                <div class="flex justify-end gap-3 mt-6">
                    <x-filament::button
                        color="gray"
                        type="button"
                        @click="isOpen = false; $wire.closeModal()"
                    >
                        Close
                    </x-filament::button>
                </div>
            @endif
        @else
            <p class="text-gray-500">No record found.</p>
        @endif
    </x-filament::modal>
</div>
