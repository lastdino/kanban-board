<div class="" x-data="{ selectedTab: 'kanban' }" >
    <div x-data="{ taskId: @js($taskId) }"
         x-init="
        if (taskId) {
            $nextTick(() => {
                setTimeout(() => {
                    $dispatch('show-modal', { id: taskId });
                }, 500);
            });
        }
     "></div>
    <div class="flex flex-col md:flex-row gap-6 justify-between md:items-center mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{route(config('kanban-board.routes.prefix').'.project_list')}}" divider="slash">{{config('kanban-board.name')}}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="" divider="slash">{{$project->title}}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex gap-4">
            <flux:radio.group variant="segmented">
                <flux:radio label="かんばん" x-on:click="selectedTab = 'kanban'" checked/>
                <flux:radio label="リスト" x-on:click="selectedTab = 'list'"/>
            </flux:radio.group>

            <flux:separator vertical class="my-2" />
            <flux:modal.trigger name="users">
                <flux:avatar.group>
                    @foreach ($users->take(3) as $user)
                        <flux:avatar size="sm" tooltip name="{{ \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($user) }}" src="{{$user->getUserAvatar()}}" />
                    @endforeach
                    @if($users->count()-3 >= 1 && $users->count() != 0)
                            <flux:avatar size="sm">{{$users->count()-3}}+</flux:avatar>
                    @endif
                </flux:avatar.group>
            </flux:modal.trigger>

            <flux:modal.trigger name="invite">
                @if($this->project->admin->id === auth()->id())
                    <flux:button variant="filled" size="sm">招待</flux:button>
                @endif
            </flux:modal.trigger>
            <flux:modal name="invite" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">ユーザーを招待</flux:heading>
                        <flux:text class="mt-2">追加するユーザーを選択して招待してください。</flux:text>
                    </div>
                    <div class="flex flex-col gap-2">
                        <select wire:model="selectedUser" class="rounded-lg border border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-800">
                            <option value="">招待する人を選択</option>
                            @foreach ($NotInvitedUsers as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary" wire:click="invite">招待</flux:button>
                    </div>
                </div>
            </flux:modal>
            <flux:modal name="users" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">ユーザーリスト</flux:heading>
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach($users as $user)
                            <div class="flex ">
                                <flux:avatar size="sm" tooltip name="{{ \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($user) }}" src="{{$user->getUserAvatar()}}" />
                                <flux:tooltip content="削除">
                                    <flux:button icon="x-mark" variant="subtle" inset wire:click="removeUser({{$user->id}})"/>
                                </flux:tooltip>
                            </div>
                        @endforeach
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>
    <div>
        <div  x-show="selectedTab === 'kanban'">
            <div class="overflow-x-auto" x-data="kanban">
                <div class="flex gap-4 min-h-fit">
                    <div class="flex gap-4 min-h-fit" x-sort="column" x-sort:group="todos">
                        @foreach ($this->columns as $column)
                            <div x-sort:item="{{ $column['id'] }}" x-data="{ open: false }">
                                <div class="rounded-lg w-80 max-w-80 bg-zinc-400/5 dark:bg-zinc-900 h-[calc(100vh-9rem)] flex flex-col">
                                    <div class="px-4 pt-4 flex justify-between items-start">
                                        <div>
                                            @if($column_title_edit == $column['id'])
                                                <flux:input wire:model="column_title" wire:keydown.enter="updateColumnTitle({{ $column['id'] }})" wire:blur="updateColumnTitle({{ $column['id'] }})" x-init="$nextTick(() => $el.focus())"/>
                                            @else
                                                <flux:button variant="ghost" size="sm" class="group relative " wire:click="editColumnTitle({{ $column['id'] }},'{{ $column['title'] }}')">{{ $column['title'] }}
                                                    <flux:icon name="pencil" class="h-4 w-4 ml-1 hidden group-hover:inline-block"/>
                                                </flux:button>
                                            @endif
                                        </div>
                                        <div>
                                            @if($this->project->users->where('id', auth()->id())->first())
                                                <flux:button variant="subtle" icon="plus" size="sm" tooltip="タスクを追加"
                                                             wire:click="dispatchTo('kanban-board.component.task-modal', 'show-new-modal',{columnId: {{ $column->id }}})"/>
                                                <flux:dropdown>
                                                    <flux:button icon="ellipsis-horizontal" size="sm" variant="subtle"></flux:button>
                                                    <flux:menu>
                                                        <flux:field>
                                                            <flux:label>ラベル</flux:label>
                                                            <div class="flex gap-2">
                                                                <flux:button variant="primary" size="xs" color="violet" wire:click="setLabelColor('violet',{{$column}})" icon="check" icon:class="{{$column->color == 'violet' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="blue" wire:click="setLabelColor('blue',{{$column}})" icon="check"
                                                                             icon:class="{{$column->color == 'blue' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="green" wire:click="setLabelColor('green',{{$column}})" icon="check"
                                                                             icon:class="{{$column->color == 'green' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="yellow" wire:click="setLabelColor('yellow',{{$column}})"
                                                                             icon="check"
                                                                             icon:class="{{$column->color == 'yellow' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="orange" wire:click="setLabelColor('orange',{{$column}})"
                                                                             icon="check"
                                                                             icon:class="{{$column->color == 'orange' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="red" wire:click="setLabelColor('red',{{$column}})" icon="check"
                                                                             icon:class="{{$column->color == 'red' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="pink" wire:click="setLabelColor('pink',{{$column}})" icon="check"
                                                                             icon:class="{{$column->color == 'pink' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                                <flux:button variant="primary" size="xs" color="zinc" wire:click="setLabelColor('zinc',{{$column}})" icon="check"
                                                                             icon:class="{{$column->color == 'zinc' ? 'opacity-100' : 'opacity-0'}}"></flux:button>
                                                            </div>
                                                        </flux:field>
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="trash" variant="danger" wire:click="removeColumn({{$column->id}})">削除</flux:menu.item>
                                                    </flux:menu>
                                                </flux:dropdown>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="px-4 flex justify-between items-end">
                                        <div></div>
                                        <flux:text class="mb-0!">{{ $column->tasks->count() }} tasks</flux:text>
                                    </div>

                                    <x-kanban-board::label_color color="{{ $column->color }}" class="rounded-lg h-1 rounded-full mt-1 flex-none"/>
                                    <div class="flex flex-col gap-2 px-2 py-2 overflow-y-auto"
                                         @dragover.prevent="autoScroll($event)"
                                         @drop="stopAutoScroll()"
                                    >
                                        <div class="flex flex-col gap-2" x-sort="handle"
                                             x-sort:group="task"
                                             data-column-id="{{ $column['id'] }}"
                                        >
                                            @php
                                                //dd($column->tasks->where('is_completed',false)->sortBy('position'));
                                            @endphp
                                            @foreach ($column->tasks->where('is_completed',false) as $card)
                                                <livewire:kanban-board.component.task-card
                                                    :task="$card"
                                                    :completed="$card->is_completed"
                                                    :key="'card-'.$card->id.'-'.$column->position.'-'.$card->updated_at.'-'.$card->position.now()"
                                                />
                                            @endforeach
                                        </div>
                                        <div x-show="open">
                                            <div>
                                                完了タスク
                                            </div>
                                            <div
                                                x-sort="handle"
                                                x-sort:group="task"
                                            >
                                                @foreach ($column->tasks->where('is_completed',true) as $card)
                                                    <livewire:kanban-board.component.task-card
                                                        :task="$card"
                                                        :completed="$card->is_completed"
                                                        :key="'card-'.$card->id.'-'.$column->position.'-'.$card->updated_at.'-'.$card->position.now()"
                                                    />
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center px-4 py-2">
                                        <flux:spacer/>
                                        @if ($column->tasks->where('is_completed',true)->count() > 0)
                                            <flux:text class="text-xs" x-on:click="open = ! open" x-text="open ? '完了タスクの非表示' : '完了タスクの表示'">非表示</flux:text>
                                        @else
                                            <div class="h-4"> </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="rounded-lg w-80 max-w-80  bg-zinc-400/5 dark:bg-zinc-900 flex flex-row gap-2 flex-none p-2 h-12 justify-between items-center" x-data="{ open: false }">
                        <flux:button variant="subtle" icon="plus" size="sm" tooltip="タスクリストを追加" x-on:click="open = ! open" x-show="!open" :disabled="!$this->project->users->where('id', auth()->id())->first()">タスクリストの追加</flux:button>
                        <flux:input wire:model="column_title" wire:keydown.enter="addColumn" @keyup.enter="open = ! open"  @click.outside="open = false" x-init="$nextTick(() => $el.focus())" x-show="open"/>
                    </div>
                </div>

            </div>
        </div>
        <div  x-show="selectedTab === 'list'">
            <div class="mt-6 shadow-xs rounded-lg border border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-800">
                <div class="overflow-x-auto h-[calc(100vh-9rem)]" >
                    <table class="min-w-full divide-y divide-zinc-800/10 dark:divide-white/20 text-zinc-800">
                        <thead class="sticky top-0 z-20 bg-gray-50 dark:bg-zinc-600">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider" wire:click="sort('title')">
                                タイトル
                                @if ($sortBy === 'title')
                                    @if ($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider" wire:click="sort('start_date')">
                                開始日
                                @if ($sortBy === 'start_date')
                                    @if ($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider" wire:click="sort('due_date')">
                                完了予定日
                                @if ($sortBy === 'due_date')
                                    @if ($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider" wire:click="sort('updated_at')">
                                完了日
                                @if ($sortBy === 'updated_at')
                                    @if ($sortDirection === 'asc')
                                        <span>↑</span>
                                    @else
                                        <span>↓</span>
                                    @endif
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider" >
                                担当者
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-800 [:where(&)]:dark:text-white uppercase tracking-wider">
                                タグ
                            </th>
                        </tr>
                        </thead>
                        @foreach ($this->columns as $column)
                            {{-- 各カラムごとに独立した Alpine スコープ --}}
                            <tbody x-data="{ showTasks: true, showCompleted: false }" class="overflow-y-auto divide-y divide-gray-200">
                            {{-- カラム見出し（クリックで未完了タスク表示のトグル） --}}
                            <tr class="sticky top-10">
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 bg-white dark:bg-zinc-800" colspan="6">
                                    <flux:heading x-on:click="showTasks = !showTasks" class="cursor-pointer">
                                        {{ $column->title }}
                                    </flux:heading>
                                    <x-kanban-board::label_color color="{{ $column->color }}" class="rounded-lg h-1 rounded-full mt-1 flex-none"/>
                                </td>
                            </tr>

                            {{-- 未完了タスク --}}
                            @if($sortDirection === 'asc')
                                @foreach($column->tasks->where('is_completed', false)->sortBy($sortBy) as $card)
                                    <tr x-show="showTasks" wire:click="$dispatch('show-modal', { id: {{ $card->id }} })">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                @if($card->isSubtask())
                                                    <div class="text-xs">#{{$card->parent->id}} {{$card->parent->title}}</div>
                                                @endif
                                                <div>@if($card->isSubtask())<span>└─ </span> @endif#{{$card->id}} {{ $card->title }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->start_date ? $card->start_date->format(config('kanban-board.datetime.formats.date')) : '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->due_date ? $card->due_date->format(config('kanban-board.datetime.formats.date'))  : ''}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($card->is_completed)
                                                {{ $card->updated_at ? $card->updated_at->format(config('kanban-board.datetime.formats.date'))  : ''}}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->assignedUser ? \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($card->assignedUser) : ''}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @foreach ($card['badges'] as $badge)
                                                <flux:badge :color="$badge['color']"
                                                            size="sm">{{ $badge['title'] }}</flux:badge>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($column->tasks->where('is_completed', false)->sortByDesc($sortBy) as $card)
                                    <tr x-show="showTasks" wire:click="$dispatch('show-modal', { id: {{ $card->id }} })">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                @if($card->isSubtask())
                                                    <div class="text-xs">#{{$card->parent->id}} {{$card->parent->title}}</div>
                                                @endif
                                                <div>@if($card->isSubtask())<span>└─ </span> @endif#{{$card->id}} {{ $card->title }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->start_date ? $card->start_date->format(config('kanban-board.datetime.formats.date')) : '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->due_date ? $card->due_date->format(config('kanban-board.datetime.formats.date'))  : ''}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($card->is_completed)
                                                {{ $card->updated_at ? $card->updated_at->format(config('kanban-board.datetime.formats.date'))  : ''}}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->assignedUser ? \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($card->assignedUser) : ''}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @foreach ($card['badges'] as $badge)
                                                <flux:badge :color="$badge['color']"
                                                            size="sm">{{ $badge['title'] }}</flux:badge>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @endif


                            {{-- 完了タスク表示切替ボタン --}}
                            @if ($column->tasks->where('is_completed', true)->count() > 0)
                                <tr x-show="showTasks">
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <flux:text class="text-xs cursor-pointer"
                                                   x-on:click="showCompleted = !showCompleted"
                                                   x-text="showCompleted ? '完了タスクの非表示' : '完了タスクの表示'">
                                        </flux:text>
                                    </td>
                                </tr>
                            @endif

                            {{-- 完了タスク一覧 --}}
                            @foreach($column->tasks->where('is_completed', true) as $card)
                                <tr x-show="showTasks && showCompleted" wire:click="$dispatch('show-modal', { id: {{ $card->id }} })">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>
                                            @if($card->isSubtask())
                                                <div class="text-xs">#{{$card->parent->id}} {{$card->parent->title}}</div>
                                            @endif
                                            <div>@if($card->isSubtask())<span>└─ </span> @endif#{{$card->id}} {{ $card->title }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->start_date ? $card->start_date->format(config('kanban-board.datetime.formats.date')) : '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->due_date ? $card->due_date->format(config('kanban-board.datetime.formats.date'))  : ''}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->updated_at ? $card->updated_at->format(config('kanban-board.datetime.formats.date'))  : ''}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->assignedUser ? \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($card->assignedUser) : ''}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @foreach ($card['badges'] as $badge)
                                            <flux:badge :color="$badge['color']"
                                                        size="sm">{{ $badge['title'] }}</flux:badge>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    <livewire:kanban-board.component.task-modal :$boardId/>
</div>


@script
<script>
    Alpine.data('kanban', () => ({
        scrollInterval: null,
        autoScroll(event) {
            const container = event.currentTarget;
            const rect = container.getBoundingClientRect();
            const scrollThreshold = 100; // スクロール開始の閾値（px）
            const scrollSpeed = 5; // スクロール速度

            // マウスの位置を取得
            const mouseY = event.clientY;
            const containerTop = rect.top;
            const containerBottom = rect.bottom;

            // 上端近くでのスクロール
            if (mouseY - containerTop < scrollThreshold) {
                this.startAutoScroll(container, -scrollSpeed);
            }
            // 下端近くでのスクロール
            else if (containerBottom - mouseY < scrollThreshold) {
                this.startAutoScroll(container, scrollSpeed);
            }
            // 中央部分では自動スクロール停止
            else {
                this.stopAutoScroll();
            }
        },
        startAutoScroll(container, speed) {
            // 既存のスクロールを停止
            this.stopAutoScroll();

            // 新しいスクロールを開始
            this.scrollInterval = setInterval(() => {
                container.scrollTop += speed;

                // スクロール限界に達したら停止
                if (speed > 0 && container.scrollTop >= container.scrollHeight - container.clientHeight) {
                    this.stopAutoScroll();
                } else if (speed < 0 && container.scrollTop <= 0) {
                    this.stopAutoScroll();
                }
            }, 16); // 約60FPS
        },

        stopAutoScroll() {
            if (this.scrollInterval) {
                clearInterval(this.scrollInterval);
                this.scrollInterval = null;
            }
        },
        handle(item, position) {

            const columns = document.querySelectorAll('[data-column-id]');
            let toColumn = '';
            columns.forEach((column) => {
                const columnId = column.getAttribute('data-column-id');
                const ids = Array.from(column.querySelectorAll('[x-sort\\:item]'))
                    .map(el => el.getAttribute('x-sort:item'))
                    .map(id => Number(id));

                if (ids.includes(item)) {
                    toColumn = columnId;
                }
            });
            if (typeof $wire !== 'undefined') {
                $wire.moveTaskToPosition(item, toColumn , position);
            }
        },

        column(item, position) {
            if (typeof $wire !== 'undefined') {
                $wire.moveColumnToPosition(item, position);
            }
        }
    }))
</script>
@endscript
