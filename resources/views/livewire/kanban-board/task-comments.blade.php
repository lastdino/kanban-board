<div>
    <div class="space-y-4">
        @if(isset($tableExists) && !$tableExists)
            <div class="text-center text-yellow-500 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                コメント機能を利用するには、マイグレーションを実行してください。<br>
                <code>php artisan migrate</code>
            </div>
        @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
            <form wire:submit.prevent="addComment" class="space-y-3">
                <flux:textarea
                    wire:model="newComment"
                    placeholder="コメントを追加..."
                    rows="3"
                />
                @error('newComment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end">
                    <flux:button type="submit">コメント追加</flux:button>
                </div>
            </form>
        </div>

        @if($comments->count() > 0)
            <div class="space-y-4">
                @foreach($comments as $comment)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between">
                            <div class="flex items-center gap-3">
                                <div class="font-medium">{{ $comment->user->name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>

                            @if($comment->user_id === auth()->id())
                                <flux:button
                                    variant="subtle"
                                    size="xs"
                                    icon="trash"
                                    wire:click="deleteComment({{ $comment->id }})"
                                    wire:confirm="このコメントを削除しますか？"
                                />
                            @endif
                        </div>

                        <div class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 py-4">
                コメントはまだありません
            </div>
        @endif
        @endif
    </div>
</div>
