<div>
    <flux:modal name="edit-task" variant="flyout" wire:model="showTaskModal">
        @if($new)
            <flux:heading size="lg">新規タスク</flux:heading>
            <div class="space-y-6 mt-6">
                <flux:input wire:model="title" label="タイトル" placeholder="タイトルを入力" />
                <flux:textarea
                    label="説明"
                    wire:model="description"
                />
                <flux:field>
                    <flux:label>ラベル</flux:label>
                    <div class="flex gap-2">
                        <flux:button variant="primary" size="xs" color="violet" wire:click="setLabelColor('violet')" icon="check" icon:class="{{$label_color == 'violet' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="blue" wire:click="setLabelColor('blue')" icon="check"
                                     icon:class="{{$label_color == 'blue' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="green" wire:click="setLabelColor('green')" icon="check"
                                     icon:class="{{$label_color == 'green' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="yellow" wire:click="setLabelColor('yellow')"
                                     icon="check"
                                     icon:class="{{$label_color == 'yellow' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="orange" wire:click="setLabelColor('orange')"
                                     icon="check"
                                     icon:class="{{$label_color == 'orange' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="red" wire:click="setLabelColor('red')" icon="check"
                                     icon:class="{{$label_color == 'red' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="pink" wire:click="setLabelColor('pink')" icon="check"
                                     icon:class="{{$label_color == 'pink' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="zinc" wire:click="setLabelColor('zinc')" icon="check"
                                     icon:class="{{$label_color == 'zinc' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                    </div>
                </flux:field>
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary" wire:click="createTask">保存</flux:button>
                </div>
            </div>

        @else
            <div class="space-y-6 mt-6">
                <div class="flex items-center gap-2">
                    @if($task)
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
                    @endif
                    @if(!$editing)
                        <div  class="flex justify-between w-full">
                            @if($title)
                                <flux:button variant="ghost" size="sm" class="group relative " wire:click="startEditing">{{ $title }}
                                    <flux:icon name="pencil" class="h-4 w-4 ml-1 hidden group-hover:inline-block"/>
                                </flux:button>
                                <div>
                                    <flux:dropdown>
                                        <flux:button icon="ellipsis-horizontal" size="xs" variant="subtle"></flux:button>
                                        <flux:menu>
                                            @if($task->isSubtask())
                                                <flux:menu.item icon="arrows-right-left" x-on:click="$flux.modal('main-task').show()">メインタスクの変更</flux:menu.item>
                                                <flux:menu.item icon="scissors" wire:click="unlinkMainTask({{ $task->id }})">メインタスクとの紐付け解除</flux:menu.item>
                                            @else
                                                <flux:menu.item icon="arrows-right-left" x-on:click="$flux.modal('main-task').show()">サブタスクに変換</flux:menu.item>
                                            @endif
                                            <flux:menu.item icon="trash" variant="danger" wire:click="DeleteSubTask({{ $task->id }})">削除</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 italic" wire:click="startEditing">クリックして編集</span>
                            @endif
                        </div>
                    @else
                        <div >
                            <flux:input wire:model="title" wire:keydown.enter="titleSave" wire:blur="titleSave" x-init="$nextTick(() => $el.focus())"/>
                        </div>
                    @endif
                </div>
                @if($task)
                    <div>
                        <flux:text class="mt-2">#{{$task->id}} タスク作成者 {{$task->createdUser->name}} 作成日 {{$task->created_at}}</flux:text>
                    </div>
                @endif
                <flux:textarea
                    label="説明"
                    wire:model="description"
                />
                <flux:input label="開始日" placeholder="" type="date" wire:model.blur="start_date"/>
                <flux:input label="完了予定" placeholder="" type="date" wire:model.blur="due_date"/>
                <flux:field>
                    <flux:label>ラベル</flux:label>
                    <div class="flex gap-2">
                        <flux:button variant="primary" size="xs" color="violet" wire:click="setLabelColor('violet')" icon="check" icon:class="{{$label_color == 'violet' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="blue" wire:click="setLabelColor('blue')" icon="check"
                                     icon:class="{{$label_color == 'blue' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="green" wire:click="setLabelColor('green')" icon="check"
                                     icon:class="{{$label_color == 'green' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="yellow" wire:click="setLabelColor('yellow')"
                                     icon="check"
                                     icon:class="{{$label_color == 'yellow' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="orange" wire:click="setLabelColor('orange')"
                                     icon="check"
                                     icon:class="{{$label_color == 'orange' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="red" wire:click="setLabelColor('red')" icon="check"
                                     icon:class="{{$label_color == 'red' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="pink" wire:click="setLabelColor('pink')" icon="check"
                                     icon:class="{{$label_color == 'pink' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                        <flux:button variant="primary" size="xs" color="zinc" wire:click="setLabelColor('zinc')" icon="check"
                                     icon:class="{{$label_color == 'zinc' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                    </div>
                </flux:field>
                <!-- サブタスク -->
                <flux:field>
                    <flux:label>
                        <flux:icon.rectangle-stack />
                        サブタスク
                    </flux:label>
                    <div class="space-y-0.5" x-data="check">
                        <div x-sort="sub" class="space-y-0.5">
                            @if($task)
                                @foreach($task->subtasks->sortby('sub_position') as $item)
                                    <div x-sort:item="{{ $item->id }}">
                                        <livewire:kanban-board.component.sub-task-list
                                            :$item
                                            :title="$item->title"
                                            :completed="$item->is_completed"
                                            :key="'sub-'.$task->id.'-'.$item->id.'-'.$item->sub_position"
                                        />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <flux:input wire:model="subTitle" wire:keydown.enter="AddSubTaskItem" placeholder="サブタスクを追加" />
                    </div>

                </flux:field>
                <!-- チェックリスト -->
                <flux:field>
                    <flux:label>
                        <flux:icon.list-bullet />
                        チェックリスト
                    </flux:label>
                    <div class="rounded-lg bg-zinc-400/5 dark:bg-zinc-900 divide-y divide-zinc-400/5 border" x-data="check">
                        <div x-sort="column">
                            @if($task)
                                @foreach($task->checklistItems->sortby('position') as $item)
                                    <div x-sort:item="{{ $item->id }}">
                                        <livewire:kanban-board.component.check-list-item
                                            :$item
                                            :content="$item->content"
                                            :completed="$item->is_completed"
                                            :key="'check-'.$task->id.'-'.$item->id.'-'.$item->position"/>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="flex items-center p-2 gap-2">
                            <div class="flex items-center justify-center w-5 h-5 rounded-full bg-white shadow-xs border border-zinc-200 dark:border-white/10 dark:bg-zinc-800">
                                <flux:icon.plus variant="mini" class="size-3"/>
                            </div>
                            <flux:separator vertical />
                            <flux:input wire:model="checkItem" wire:keydown.enter="AddCheckItem" placeholder="チェックリスト項目を追加" />
                        </div>
                    </div>

                </flux:field>
            </div>
        @endif


    </flux:modal>
    <flux:modal name="main-task" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">メインタスク変更</flux:heading>
                <flux:text class="mt-2">変更先のメインタスクを選択</flux:text>
            </div>
            <flux:input size="sm" placeholder="メインタスクを検索" wire:model.live.debounce.300ms="search"/>
            <div class="space-y-2">
                @foreach($this->MainTask as $parent)
                    <flux:button size="xs" wire:click="changeMainTask({{ $parent->id }})">{{$parent->title}}</flux:button>
                @endforeach
            </div>
        </div>
    </flux:modal>
</div>
@script
<script>
    Alpine.data('check', () => ({
        column(item, position) {
            if (typeof $wire !== 'undefined') {
                $wire.moveCheckListItemToPosition(item, position);
            }
        },
        sub(item, position) {
            if (typeof $wire !== 'undefined') {
                $wire.moveSubTaskItemToPosition(item, position);
            }
        },
    }))
</script>
@endscript
