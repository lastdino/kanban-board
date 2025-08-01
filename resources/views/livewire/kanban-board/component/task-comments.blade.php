<div>
    <div  class="flex flex-col space-y-2 max-h-[400px] overflow-y-auto mt-2">
        @foreach($comments as $comment)
            <flux:callout >
                <div class="flex justify-between">
                    <flux:callout.heading>
                        <flux:avatar size="sm" tooltip name="{{ $comment->user->name }}" src="{{$comment->user->getUserAvatar}}" />
                        {{ $comment->user->name }}
                        <flux:text class="text-xs">{{ $comment->created_at->diffForHumans() }}</flux:text>
                    </flux:callout.heading>
                    <flux:tooltip content="返信">
                        <flux:button size="xs"  icon="arrow-uturn-left" variant="ghost" inset wire:click="replyComment({{ $comment->id }})"/>
                    </flux:tooltip>
                </div>
                <flux:callout.text>
                    {{ $comment->content }}
                </flux:callout.text>
                @if($comment->reply_id != null)
                    <div>
                        <flux:callout >
                            <div class="flex justify-between">
                                <flux:callout.heading>
                                    <flux:avatar size="sm" tooltip name="{{ $comment->reply->user->name }}" src="{{$comment->reply->user->getUserAvatar}}" />
                                    {{ $comment->reply->user->name }}
                                    <flux:text class="text-xs">{{ $comment->reply->created_at->diffForHumans() }}</flux:text>
                                </flux:callout.heading>
                            </div>
                            <flux:callout.text>
                                {{ $comment->reply->content }}
                            </flux:callout.text>
                        </flux:callout>
                    </div>
                @endif
            </flux:callout>
        @endforeach
    </div>

        <div class="mt-4">
            @if($replycommentId)
                @php
                    $reply=$comments->where('id',$replycommentId)->first()
                @endphp
            <div class="flex">
                <flux:icon.arrow-uturn-left class="size-3" />
                <flux:text>返信:{{$reply->content}}</flux:text>
            </div>
            @endif
            <flux:textarea
                placeholder="コメントを入力..."
                wire:model="commentContent"
            />
            <div class="flex justify-end mt-2">
                <flux:button variant="primary" wire:click="addComment">
                    コメントを追加
                </flux:button>
            </div>
        </div>
</div>

