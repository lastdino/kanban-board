<div class="flex items-center p-2 gap-2">
    @if($item->is_completed)
        <flux:tooltip content="クリックして未完了">
            <flux:checkbox wire:model.live.debounce="completed"
                           :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
        </flux:tooltip>
    @else
        <flux:tooltip content="クリックして完了">
            <flux:checkbox wire:model.live.debounce="completed"
                           :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
        </flux:tooltip>
    @endif
    <flux:separator vertical />
    <div class="w-full flex flex-row items-center gap-2 group/item">
        @if($not_edit)
            <div class="w-full flex flex-row items-center gap-2 group/item">
                <flux:text >{{ $content }}</flux:text>
            </div>
        @elseif(!$editing)
            <div @if($this->project->users->where('id', auth()->id())->first()) wire:click="startEditing"
                 @endif class="w-full group/content">
                @if($content)
                    @if($item->is_completed)
                        <flux:button variant="ghost" size="sm" class="relative line-through w-full justify-start" >{{ $content }}
                            <flux:icon.pencil class="size-4 ml-1 hidden group-hover/content:inline-block"/>
                        </flux:button>
                    @else
                        <flux:button variant="ghost" size="sm" class="relative w-full justify-start" >{{ $content }}
                            <flux:icon.pencil class="size-4 ml-1 hidden group-hover/content:inline-block"/>
                        </flux:button>
                    @endif

                @else
                    <span class="text-gray-400 dark:text-gray-500 italic">クリックして編集</span>
                @endif
            </div>
            <flux:button size="xs" icon="trash" variant="ghost" inset class="opacity-0 group-hover/item:opacity-100" wire:click="$parent.DeleteCheckItem({{ $item->id }})" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
        @else
            <div >
                <flux:input wire:model="content" wire:keydown.enter="contentSave" wire:blur="contentSave" x-init="$nextTick(() => $el.focus())"/>
            </div>
        @endif
    </div>
</div>

