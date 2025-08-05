<div>
    <flux:modal name="edit-task" variant="flyout" x-data="{ selectedTab: 'task' }" x-on:close="selectedTab = 'task'">
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
            <div >
                <flux:radio.group variant="segmented">
                    <flux:radio label="タスク" x-on:click="selectedTab = 'task'" x-bind:checked="selectedTab === 'task'"/>
                    <flux:radio label="コメント" x-on:click="selectedTab = 'comment'" />
                    <flux:radio label="ファイル" x-on:click="selectedTab = 'file'"/>
                </flux:radio.group>
                <div  x-show="selectedTab === 'task'">
                    <div class="space-y-6 mt-6">
                        @if($task)
                            <div class="flex items-center gap-2">
                                <div x-on:click.stop>
                                    @if($task->is_completed)
                                        <flux:tooltip content="クリックして未完了">
                                            <flux:checkbox wire:model.live.debounce="completed" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip content="クリックして完了">
                                            <flux:checkbox wire:model.live.debounce="completed" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                                        </flux:tooltip>
                                    @endif
                                </div>
                                @if(!$editing)
                                    <div  class="flex justify-between w-full">
                                        @if($title)
                                            <flux:button variant="ghost" size="sm" class="group relative " wire:click="startEditing" :disabled="!$this->project->users->where('id', auth()->id())->first()">{{ $title }}
                                                <flux:icon name="pencil" class="h-4 w-4 ml-1 hidden group-hover:inline-block"/>
                                            </flux:button>
                                            <div>
                                                <flux:dropdown>
                                                    <flux:button icon="ellipsis-horizontal" size="xs" variant="subtle" :disabled="!$this->project->users->where('id', auth()->id())->first()"></flux:button>
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
                            <div>
                                <flux:text class="mt-2">#{{$task->id}} タスク作成者 {{$task->createdUser->name}} 作成日 {{$task->created_at}}</flux:text>
                            </div>
                            <flux:textarea
                                label="説明"
                                wire:model="description"
                                :disabled="!$this->project->users->where('id', auth()->id())->first()"
                            />
                            <flux:input label="開始日" placeholder="" type="date" wire:model.blur="start_date" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                            <flux:input label="完了予定" placeholder="" type="date" wire:model.blur="due_date" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                            <flux:input label="リマインド" placeholder="" type="date" wire:model.blur="reminder_at" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                            <flux:select wire:model.blur="assigned_user" label="担当者" placeholder="担当者を選択" :disabled="!$this->project->users->where('id', auth()->id())->first()">
                                @foreach($this->Users as $user)
                                    <flux:select.option value="{{$user->id}}">{{$user->name}}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <!-- レビュワー -->
                            <flux:field>
                                <flux:label>レビュワー</flux:label>
                                <div class="flex gap-2 flex-wrap">
                                    <flux:button variant="subtle" icon="plus" size="sm" tooltip="レビュワーを追加"  wire:click="openReviewerModal" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                                    @foreach($task->reviewers as $reviewer)
                                        <div class="flex items-center gap-2 sm:gap-4 relative" >
                                            <flux:avatar size="sm" tooltip name="{{ $reviewer->name }}" src="{{$reviewer->getUserAvatar()}}" />
                                            <div class="absolute -top-2 -right-2">
                                                @if($this->project->users->where('id', auth()->id())->first())
                                                    <flux:icon.x-mark class="size-5 pl-2" wire:click="removeReviewer({{ $reviewer->id }})"/>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <flux:modal name="edit-reviewer" class="md:w-96">
                                    <form wire:submit.prevent="addReviewer">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">レビュワー</flux:heading>
                                            </div>
                                            <flux:input wire:model.live="name" placeholder="検索"/>
                                            <div class="space-y-2">
                                                @foreach($this->notInReviewers as $user)
                                                    <label class="flex items-center gap-2">
                                                        <input type="checkbox" wire:model.defer="review_user" value="{{ $user->id }}">
                                                        <flux:heading> {{ $user->name }}</flux:heading>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <div class="flex">
                                                <flux:spacer />
                                                <flux:button type="submit" variant="primary">追加</flux:button>
                                            </div>
                                        </div>
                                    </form>
                                </flux:modal>
                            </flux:field>
                            <!-- フォロワー -->
                            <flux:field>
                                <flux:label>フォロワー</flux:label>
                                <div class="flex gap-2 flex-wrap">
                                    <flux:button variant="subtle" icon="plus" size="sm" tooltip="フォロワーを追加"  wire:click="openFollowerModal" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                                    @foreach($task->followers as $follower)
                                        <div class="flex items-center gap-2 sm:gap-4 relative" >
                                            <flux:avatar size="sm" tooltip name="{{ $follower->name }}" src="{{$follower->getUserAvatar()}}" />
                                            <div class="absolute -top-2 -right-2">
                                                @if($this->project->users->where('id', auth()->id())->first())
                                                    <flux:icon.x-mark class="size-5 pl-2" wire:click="removeFollower({{ $follower->id }})"/>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <flux:modal name="edit-follower" class="md:w-96">
                                    <form wire:submit.prevent="addFollower">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">フォロワー</flux:heading>
                                            </div>
                                            <flux:input wire:model.live="name" placeholder="検索"/>
                                            <div class="space-y-2">
                                                @foreach($this->notInFollowers as $user)
                                                    <label class="flex items-center gap-2">
                                                        <input type="checkbox" wire:model.defer="follow_user" value="{{ $user->id }}">
                                                        <flux:heading> {{ $user->name }}</flux:heading>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <div class="flex">
                                                <flux:spacer />
                                                <flux:button type="submit" variant="primary">追加</flux:button>
                                            </div>
                                        </div>
                                    </form>
                                </flux:modal>
                            </flux:field>
                            <!-- タグ -->
                            <flux:field>
                                <flux:label>
                                    <flux:icon.tag />
                                    タグ
                                </flux:label>
                                <div class="flex gap-2 flex-wrap">
                                    <flux:button variant="subtle" icon="plus" size="sm" tooltip="タグを追加"  wire:click="openTagModal" :disabled="!$this->project->users->where('id', auth()->id())->first()"/>
                                    @if($task)
                                        @foreach($task->badges as $badge)
                                            <flux:badge :color="$badge['color']" size="sm">
                                                {{ $badge['title'] }}
                                                @if($this->project->users->where('id', auth()->id())->first())
                                                    <flux:icon.x-mark class="size-5 pl-2" wire:click="removeBadge({{ $badge['id'] }})"/>
                                                @endif
                                            </flux:badge>
                                        @endforeach
                                    @endif
                                </div>
                                <flux:modal name="edit-tags" class="md:w-96">
                                    <div class="space-y-6">
                                        <form wire:submit.prevent="addBadge">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">タグを追加</flux:heading>
                                                </div>
                                                <flux:input wire:model.live="search_tag" placeholder="検索"/>
                                                <div class="space-y-2">
                                                    @foreach($this->notInBadges as $tag)
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" wire:model.defer="tags" value="{{ $tag->id }}">
                                                            <flux:heading> {{ $tag->title }}</flux:heading>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <div class="flex">
                                                    <flux:spacer />
                                                    <flux:button type="submit" variant="primary">追加</flux:button>
                                                </div>
                                            </div>
                                        </form>
                                        <flux:modal.trigger name="add-tag">
                                            <flux:button icon="plus" class="w-full">新しいタグの追加</flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </flux:modal>
                                <flux:modal name="add-tag" class="md:w-96">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">新しいタグを追加</flux:heading>
                                        </div>
                                        <flux:input label="タグ名" placeholder="タグ名" wire:model.live.debounce="tag_name"/>

                                        <flux:field>
                                            <flux:label>近いタグ</flux:label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($this->Badges as $badge)
                                                    <flux:badge :color="$badge['color']" size="sm">
                                                        {{ $badge['title'] }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>色</flux:label>
                                            <div class="flex gap-2">
                                                <flux:button variant="primary" size="xs" color="violet" wire:click="setTagColor('violet')" icon="check" icon:class="{{$tag_color == 'violet' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="blue" wire:click="setTagColor('blue')" icon="check"
                                                             icon:class="{{$tag_color == 'blue' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="green" wire:click="setTagColor('green')" icon="check"
                                                             icon:class="{{$tag_color == 'green' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="yellow" wire:click="setTagColor('yellow')"
                                                             icon="check"
                                                             icon:class="{{$tag_color == 'yellow' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="orange" wire:click="setTagColor('orange')"
                                                             icon="check"
                                                             icon:class="{{$tag_color == 'orange' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="red" wire:click="setTagColor('red')" icon="check"
                                                             icon:class="{{$tag_color == 'red' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="pink" wire:click="setTagColor('pink')" icon="check"
                                                             icon:class="{{$tag_color == 'pink' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                <flux:button variant="primary" size="xs" color="zinc" wire:click="setTagColor('zinc')" icon="check"
                                                             icon:class="{{$tag_color == 'zinc' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                            </div>
                                        </flux:field>
                                        <div class="flex">
                                            <flux:spacer />
                                            <flux:button type="submit" variant="primary" wire:click="addNewBadge">作成</flux:button>
                                        </div>
                                    </div>
                                </flux:modal>

                            </flux:field>
                            <!-- ラベル -->
                            <flux:field>
                                <flux:label>ラベル</flux:label>
                                <div class="flex gap-2">
                                    <flux:button variant="primary" size="xs" color="violet" wire:click="setLabelColor('violet')" icon="check" icon:class="{{$label_color == 'violet' ? 'opacity-100' : 'opacity-0'}}" :disabled="!$this->project->users->where('id', auth()->id())->first()"></flux:button>
                                    <flux:button variant="primary" size="xs" color="blue" wire:click="setLabelColor('blue')" icon="check"
                                                 icon:class="{{$label_color == 'blue' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="green" wire:click="setLabelColor('green')" icon="check"
                                                 icon:class="{{$label_color == 'green' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="yellow" wire:click="setLabelColor('yellow')"
                                                 icon="check"
                                                 icon:class="{{$label_color == 'yellow' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="orange" wire:click="setLabelColor('orange')"
                                                 icon="check"
                                                 icon:class="{{$label_color == 'orange' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="red" wire:click="setLabelColor('red')" icon="check"
                                                 icon:class="{{$label_color == 'red' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="pink" wire:click="setLabelColor('pink')" icon="check"
                                                 icon:class="{{$label_color == 'pink' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
                                    <flux:button variant="primary" size="xs" color="zinc" wire:click="setLabelColor('zinc')" icon="check"
                                                 icon:class="{{$label_color == 'zinc' ? 'opacity-100' : 'opacity-0'}}"
                                                 :disabled="!$this->project->users->where('id', auth()->id())->first()"
                                    ></flux:button>
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
                                    </div>
                                    @if($this->project->users->where('id', auth()->id())->first())
                                        <flux:input wire:model="subTitle" wire:keydown.enter="AddSubTaskItem" placeholder="サブタスクを追加" />
                                    @endif
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
                                        @foreach($task->checklistItems->sortby('position') as $item)
                                            <div x-sort:item="{{ $item->id }}">
                                                <livewire:kanban-board.component.check-list-item
                                                    :$item
                                                    :content="$item->content"
                                                    :completed="$item->is_completed"
                                                    :key="'check-'.$task->id.'-'.$item->id.'-'.$item->position"/>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center p-2 gap-2">
                                        @if($this->project->users->where('id', auth()->id())->first())
                                            <div class="flex items-center justify-center w-5 h-5 rounded-full bg-white shadow-xs border border-zinc-200 dark:border-white/10 dark:bg-zinc-800">
                                                <flux:icon.plus variant="mini" class="size-3"/>
                                            </div>
                                            <flux:separator vertical />
                                            <flux:input wire:model="checkItem" wire:keydown.enter="AddCheckItem" placeholder="チェックリスト項目を追加" />
                                        @endif
                                    </div>
                                </div>

                            </flux:field>
                        @endif
                    </div>
                </div>
                <div  x-show="selectedTab === 'comment'">
                    @if($task)
                        <livewire:kanban-board.component.task-comments :taskId="$task->id"/>
                    @endif
                </div>
                <div  x-show="selectedTab === 'file'">
                    @if($task)
                        <livewire:kanban-board.component.task-file :taskId="$task->id"/>
                    @endif
                </div>

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
