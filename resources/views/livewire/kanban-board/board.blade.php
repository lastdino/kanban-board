<div class="">
    <div class="overflow-x-auto" x-data="kanban">
        <div class="flex gap-4 min-h-fit" x-sort="column" x-sort:group="todos">
            @foreach ($this->columns as $column)
                <div x-sort:item="{{ $column['id'] }}" x-data="{ open: false }">
                    <div class="rounded-lg w-80 max-w-80 bg-zinc-400/5 dark:bg-zinc-900 h-[calc(100vh-6rem)] flex flex-col">
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
                        <x-kanban-board::label_color color="{{ $column->color }}" class="rounded-lg h-1 rounded-full mt-1 flex-none">

                        </x-kanban-board::label_color>
                        <div class="flex flex-col gap-2 px-2 py-2 overflow-y-auto"
                             @dragover.prevent="autoScroll($event)"
                             @drop="stopAutoScroll()"
                        >
                            <div class="flex flex-col gap-2" x-sort="handle"
                                 x-sort:group="task"
                                 data-column-id="{{ $column['id'] }}"
                            >
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
