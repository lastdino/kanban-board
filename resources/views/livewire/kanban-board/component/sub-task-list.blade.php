<div class="flex items-center p-2 gap-2  bg-zinc-400/5 dark:bg-zinc-900 border rounded-lg ">
    @if($item->is_completed)
        <flux:tooltip content="{{ __('kanban-board::messages.mark_incomplete') }}">
            <flux:checkbox wire:model.live.debounce="completed" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
        </flux:tooltip>
    @else
        <flux:tooltip content="{{ __('kanban-board::messages.mark_completed') }}">
            <flux:checkbox wire:model.live.debounce="completed" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
        </flux:tooltip>
    @endif
    <flux:separator vertical />
    <div class="w-full" wire:click="$dispatch('show-modal', { id: {{ $item->id }} })">{{ $title }}</div>
    <div>
        <flux:dropdown>
            <flux:button icon="ellipsis-vertical" size="xs" variant="subtle" :disabled="!$this->project->users->where('id', auth()->id())->first()"></flux:button>
            <flux:menu>
                <flux:menu.item icon="arrows-right-left" x-on:click="$flux.modal('main-task-{{ $item->id }}').show()">{{ __('kanban-board::messages.change_main_task') }}</flux:menu.item>
                <flux:menu.item icon="scissors" wire:click="$parent.unlinkMainTask({{ $item->id }})">{{ __('kanban-board::messages.unlink_main_task') }}</flux:menu.item>
                <flux:menu.item icon="trash" variant="danger" wire:click="$parent.DeleteSubTask({{ $item->id }})">{{ __('kanban-board::messages.delete') }}</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
    <div>
        <flux:modal name="main-task-{{ $item->id }}" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('kanban-board::messages.change_main_task_title') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('kanban-board::messages.select_main_task') }}</flux:text>
                </div>
                <flux:input size="sm" placeholder="{{ __('kanban-board::messages.search_main_task') }}" wire:model.live.debounce.300ms="search"/>
                <div class="space-y-2">
                    @foreach($this->MainTask as $parent)
                        <flux:button size="xs" wire:click="$parent.changeMainTask({{ $parent->id }},{{ $item->id }})">{{$parent->title}}</flux:button>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    </div>
</div>
