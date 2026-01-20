<div class="py-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.project_list') }}" divider="slash">{{ config('kanban-board.name') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.board', ['boardId' => $project->id]) }}" divider="slash">{{ $project->title }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('kanban-board::messages.file_list') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="mt-6">
        <flux:table :paginate="$files">
            <flux:table.columns>
                <flux:table.column>{{ __('kanban-board::messages.file_name') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.task') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.size') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.upload_date') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($files as $file)
                    <flux:table.row :key="$file->id">
                        <flux:table.cell class="font-medium">
                            {{ $file->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $file->model->title ?? 'N/A' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ number_format($file->size / 1024, 2) }} KB
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $file->created_at->format('Y-m-d H:i') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="subtle" wire:click="download({{ $file->id }})">{{ __('kanban-board::messages.download') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-zinc-500">
                            {{ __('kanban-board::messages.no_files') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
