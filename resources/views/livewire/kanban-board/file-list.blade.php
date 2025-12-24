<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.project_list') }}" divider="slash">{{ config('kanban-board.name') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.board', ['boardId' => $project->id]) }}" divider="slash">{{ $project->title }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('kanban-board::messages.file_list') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="mt-6 bg-white dark:bg-zinc-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('kanban-board::messages.file_name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('kanban-board::messages.task') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('kanban-board::messages.size') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('kanban-board::messages.upload_date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('kanban-board::messages.operation') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse ($files as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $file->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $file->model->title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format($file->size / 1024, 2) }} KB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $file->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <flux:button size="sm" variant="subtle" wire:click="download({{ $file->id }})">{{ __('kanban-board::messages.download') }}</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('kanban-board::messages.no_files') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/10">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
