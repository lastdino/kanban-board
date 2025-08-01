<div
    class="bg-white rounded-lg shadow-xs border border-zinc-200 dark:border-white/10 dark:bg-zinc-800 space-y-2 w-full"
    x-sort:item="{{ $task['id'] }}">
    <div class="p-3 " wire:click="$dispatch('show-modal', { id: {{ $task->id }} })">
        @if($task->isSubtask())
            <flux:heading>
                <flux:icon.rectangle-stack class="size-3 inline"/>
                #{{$task->parent->id}} {{$task->parent->title}}
            </flux:heading>
        @endif
        <div  class="{{ $task->isSubtask() ? 'space-y-2 ml-4 rounded-lg shadow-xs border border-zinc-200 dark:border-white/10 dark:bg-zinc-800 p-3' : 'space-y-2' }}">
            <div class="flex flex-wrap gap-2" >
                @foreach ($task['badges'] as $badge)
                    <flux:badge :color="$badge['color']"
                                size="sm">{{ $badge['title'] }}</flux:badge>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                <div x-on:click.stop>
                    @if($task->is_completed)
                        <flux:tooltip content="クリックして未完了">
                            <flux:checkbox wire:model.live.debounce="completed" />
                        </flux:tooltip>
                    @else
                        <flux:tooltip content="クリックして完了">
                            <flux:checkbox wire:model.live.debounce="completed" />
                        </flux:tooltip>
                    @endif
                </div>
                <flux:heading>#{{$task['id']}}{{ $task['title'] }}</flux:heading>
            </div>
            @if($task['label_color'])
                <x-kanban-board::label_color class="h-1 rounded-full mt-1" color="{{ $task['label_color'] }}"/>
            @endif
            @if($task->hasChecklist())
                <div class="rounded-lg bg-zinc-400/5 dark:bg-zinc-900 divide-y divide-zinc-400/5 border">
                    <div >
                        @foreach($task->checklistItems->where('is_completed',false)->sortby('position') as $item)
                            <div x-on:click.stop>
                                <livewire:kanban-board.component.check-list-item
                                    :$item
                                    :content="$item->content"
                                    :completed="$item->is_completed"
                                    :not_edit="true"
                                    :key="$task->id.'-'.$item->id.'-'.$item->position.'-'.$task->position"/>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($task['due_date']) && $task['due_date'])
                <div class="flex items-center gap-1 mt-2">
                    <flux:text class="text-xs" color="{{ $task->isOverdue() ? 'red' : ($task->isDueSoon() ? 'yellow' : '') }}">
                        <flux:icon name="calendar" class="w-3 h-3 inline"/>
                        {{ \Carbon\Carbon::parse($task['due_date'])->format('Y/m/d') }}
                    </flux:text>
                </div>
            @endif

            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center">
                    @if($task->hasSubtasks())
                        <span class="text-xs text-gray-500">
                                                        <flux:icon.rectangle-stack class="size-3 inline"/>
                                                        {{ $task->subtasks->where('is_completed', true)->count() }} /{{ $task->subtasks->count() }}
                                                    </span>
                    @endif
                    @if($task->hasChecklist())
                        <span class="text-xs text-gray-500 ml-2">
                                                        <flux:icon.list-bullet class="size-3 inline"/>
                                                        {{ $task->completed_checklist_items_count }} /{{ $task->checklist_items_count }}
                                                    </span>
                    @endif
                </div>


                @if(isset($task['comments_count']))
                    <span class="text-xs text-gray-500">
                                            <flux:icon name="chat-bubble-left" class="w-3 h-3 inline"/>
                                            {{ $task['comments_count'] > 0 ? $task['comments_count'] : '' }}
                                        </span>
                @endif
            </div>
        </div>
    </div>
</div>
