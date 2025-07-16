<div class="" x-data="{ selectedTab: 'kanban' }" >
    <div class="flex flex-col md:flex-row gap-6 justify-between md:items-center mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#" divider="slash">Acme Inc.</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#" divider="slash">iOS App V2</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex gap-4">
            <flux:radio.group variant="segmented">
                <flux:radio label="かんばん" x-on:click="selectedTab = 'kanban'" checked/>
                <flux:radio label="リスト" x-on:click="selectedTab = 'list'"/>
            </flux:radio.group>

            <flux:separator vertical class="my-2" />
            <flux:avatar.group>
                @foreach (['Caleb Porzio', 'River Porzio', 'Knox Porzio'] as $item)
                    <flux:avatar size="sm" tooltip name="{{ $item }}" src="https://i.pravatar.cc/100?img={{ $loop->index + 12 }}" />
                @endforeach
                <flux:avatar size="sm">3+</flux:avatar>
            </flux:avatar.group>
            <flux:button variant="filled" size="sm">Invite</flux:button>
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
                                            <flux:heading>{{ $column['title'] }}</flux:heading>
                                            <flux:subheading class="mb-0!">{{ $column->tasks->count() }} tasks</flux:subheading>
                                        </div>
                                        <div>
                                            <flux:button variant="subtle" icon="plus" size="sm" tooltip="タスクを追加" wire:click="dispatchTo('kanban-board.component.task-modal', 'show-new-modal',{columnId: {{ $column->id }}})"/>
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
                                        </div>
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
                        <flux:button variant="subtle" icon="plus" size="sm" tooltip="タスクリストを追加" x-on:click="open = ! open" x-show="!open">タスクリストの追加</flux:button>
                        <flux:input wire:model="column_title" wire:keydown.enter="addColumn" @keyup.enter="open = ! open" x-init="$nextTick(() => $el.focus())" x-show="open"/>
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
                                    <tr x-show="showTasks" wire:click="$dispatchTo('kanban-board.component.task-modal', 'show-modal', { id: {{ $card->id }} })">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->start_date->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->due_date->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->updated_at->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->assignedUser->name }}</td>
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
                                    <tr x-show="showTasks">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->start_date->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->due_date->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->updated_at->format('Y/m/d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->assignedUser->name }}</td>
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
                                <tr x-show="showTasks && showCompleted">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->title }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    <livewire:kanban-board.component.task-modal/>
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
