<div>
    @if($analysis)
        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center mb-3">
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Conversation Thread
            </h3>

            {{-- Scrollable chat area --}}
            <div class="max-h-64 overflow-y-auto space-y-3 mb-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg"
                 id="chat-scroll-{{ $soilAnalysisId }}"
                 wire:poll.5s>

                {{-- Initial expert recommendation --}}
                @if($analysis->expert_comments)
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-blue-100 dark:bg-blue-900/40 rounded-lg p-2.5">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">
                                    {{ $analysis->validator?->name ?? 'Expert' }}
                                </span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-blue-200 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded">Initial Recommendation</span>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $analysis->expert_comments }}</p>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                {{ $analysis->validated_at?->format('M d, Y H:i') }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Initial farmer reply --}}
                @if($analysis->farmer_reply)
                    <div class="flex justify-end">
                        <div class="max-w-[80%] bg-amber-100 dark:bg-amber-900/40 rounded-lg p-2.5">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">
                                    {{ $analysis->farmer ? trim($analysis->farmer->firstname . ' ' . $analysis->farmer->lastname) : 'Farmer' }}
                                </span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-300 rounded">Initial Reply</span>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $analysis->farmer_reply }}</p>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                {{ $analysis->farmer_reply_date?->format('M d, Y H:i') }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Conversation thread messages --}}
                @forelse($analysis->conversations as $msg)
                    <div class="flex {{ $msg->sender_type === 'expert' ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[80%] {{ $msg->sender_type === 'expert'
                            ? 'bg-blue-100 dark:bg-blue-900/40'
                            : 'bg-amber-100 dark:bg-amber-900/40' }} rounded-lg p-2.5">
                            <span class="text-xs font-semibold {{ $msg->sender_type === 'expert'
                                ? 'text-blue-800 dark:text-blue-300'
                                : 'text-amber-800 dark:text-amber-300' }}">
                                {{ $msg->sender_name }}
                            </span>
                            <p class="text-sm text-gray-900 dark:text-gray-100 mt-0.5">{{ $msg->message }}</p>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                {{ $msg->created_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                    </div>
                @empty
                    @if(!$analysis->expert_comments && !$analysis->farmer_reply)
                        <p class="text-xs text-center text-gray-400 py-4">No messages yet.</p>
                    @endif
                @endforelse
            </div>

            {{-- Expert reply input --}}
            <form wire:submit="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    wire:model="newMessage"
                    placeholder="Type your message..."
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500"
                    maxlength="2000"
                />
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="sendMessage">Send</span>
                    <span wire:loading wire:target="sendMessage">Sending...</span>
                </button>
            </form>
            @error('newMessage')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
    @endif
</div>
