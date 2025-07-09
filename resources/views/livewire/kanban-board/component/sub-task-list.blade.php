<div class="flex items-center p-2 gap-2  bg-zinc-400/5 dark:bg-zinc-900 border rounded-lg ">
    @if($item->is_completed)
        <flux:tooltip content="クリックして未完了">
            <flux:checkbox wire:model.live.debounce="completed" />
        </flux:tooltip>
    @else
        <flux:tooltip content="クリックして完了">
            <flux:checkbox wire:model.live.debounce="completed" />
        </flux:tooltip>
    @endif
    <flux:separator vertical />
    <div class="w-full" wire:click="$dispatchTo('kanban-board.component.task-modal', 'show-modal', { id: {{ $item->id }} })">{{ $title }}</div>
    <div>
        <flux:dropdown>
            <flux:button icon="ellipsis-vertical" size="xs" variant="subtle"></flux:button>
            <flux:menu>
                <flux:menu.item icon="arrows-right-left" x-on:click="$flux.modal('main-task-{{ $item->id }}').show()">メインタスクの変更</flux:menu.item>
                <flux:menu.item icon="scissors" wire:click="$parent.unlinkMainTask({{ $item->id }})">メインタスクとの紐付け解除</flux:menu.item>
                <flux:menu.item icon="trash" variant="danger" wire:click="$parent.DeleteSubTask({{ $item->id }})">削除</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
    <div>
        <flux:modal name="main-task-{{ $item->id }}" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">メインタスク変更</flux:heading>
                    <flux:text class="mt-2">変更先のメインタスクを選択</flux:text>
                </div>
                <flux:input size="sm" placeholder="メインタスクを検索" wire:model.live.debounce.300ms="search"/>
                <div class="space-y-2">
                    @foreach($this->MainTask as $parent)
                        <flux:button size="xs" wire:click="$parent.changeMainTask({{ $parent->id }},{{ $item->id }})">{{$parent->title}}</flux:button>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    </div>
</div>
