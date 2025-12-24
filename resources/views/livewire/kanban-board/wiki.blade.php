<div class="p-6">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.project_list') }}" divider="slash">{{ config('kanban-board.name') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route(config('kanban-board.routes.prefix').'.board', ['boardId' => $project->id]) }}" divider="slash">{{ $project->title }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Wiki</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex h-[calc(100vh-12rem)] gap-6">
        <!-- Sidebar: Page List -->
        <div class="w-64 flex flex-col gap-4 border-r pr-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('kanban-board::messages.wiki_pages') }}</flux:heading>
                <flux:button wire:click="createPage" icon="plus" variant="subtle" size="sm" />
            </div>

            <nav class="flex flex-col gap-1 overflow-y-auto">
                @foreach($project->wikiPages as $page)
                    <div class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm {{ $selectedPageId == $page->id ? 'bg-zinc-100 dark:bg-zinc-800 font-medium' : 'hover:bg-zinc-50 dark:hover:bg-zinc-900' }}">
                        <button
                            wire:click="selectPage({{ $page->id }})"
                            class="flex-1 text-left truncate"
                        >
                            {{ $page->title }}
                        </button>

                        <flux:dropdown>
                            <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" class="opacity-0 group-hover:opacity-100" />
                            <flux:menu>
                                <flux:menu.item wire:click="deletePage({{ $page->id }})" variant="danger" icon="trash">{{ __('kanban-board::messages.delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                @endforeach
            </nav>
        </div>

        <!-- Main Content: Viewer/Editor -->
        <div class="flex-1 overflow-y-auto">
            @if($isEditing)
                <div class="flex flex-col gap-4 max-w-4xl">
                    <flux:input wire:model="title" label="{{ __('kanban-board::messages.title') }}" placeholder="{{ __('kanban-board::messages.title') }}" />

                    <flux:field>
                        <flux:label>{{ __('kanban-board::messages.content') }} ({{ __('kanban-board::messages.markdown_supported') }})</flux:label>
                        <flux:textarea wire:model="content" placeholder="{{ __('kanban-board::messages.content') }}" rows="15" />
                    </flux:field>

                    <div class="flex gap-2 justify-end">
                        <flux:button wire:click="cancel" variant="ghost">{{ __('kanban-board::messages.cancel') }}</flux:button>
                        <flux:button wire:click="save" variant="primary">{{ __('kanban-board::messages.save_page') }}</flux:button>
                    </div>
                </div>
            @elseif($selectedPageId)
                <div class="max-w-5xl">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <flux:heading size="xl">{{ $title }}</flux:heading>
                        <flux:button wire:click="editPage" icon="pencil-square" variant="subtle" size="sm">{{ __('kanban-board::messages.edit') }}</flux:button>
                    </div>

                    <div class="border rounded-lg bg-white dark:bg-zinc-900 overflow-hidden shadow-xs">
                        <!-- Content Header (GitHub style) -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-2 border-b flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs text-zinc-500">
                                <flux:icon icon="book-open" variant="mini" class="size-4" />
                                <span>Markdown</span>
                            </div>
                        </div>

                        <!-- Markdown Body -->
                        <div class="p-8 md:p-12">
                            <article class="markdown-body prose dark:prose-invert prose-zinc max-w-none
                                prose-headings:border-b prose-headings:pb-2 prose-headings:font-semibold
                                prose-h1:text-3xl prose-h2:text-2xl prose-h2:mt-8
                                prose-pre:bg-zinc-50 dark:prose-pre:bg-zinc-950 prose-pre:border prose-pre:rounded-lg prose-pre:p-4
                                prose-code:text-zinc-800 dark:prose-code:text-zinc-200 prose-code:bg-zinc-100 dark:prose-code:bg-zinc-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:before:content-none prose-code:after:content-none
                                prose-img:rounded-lg prose-img:shadow-md
                                prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline
                                prose-blockquote:border-l-4 prose-blockquote:border-zinc-300 dark:prose-blockquote:border-zinc-700 prose-blockquote:italic
                                prose-table:border prose-table:border-collapse prose-th:bg-zinc-50 dark:prose-th:bg-zinc-800 prose-th:p-2 prose-td:p-2 prose-td:border">
                                {!! \Illuminate\Support\Str::markdown($content) !!}
                            </article>
                        </div>
                    </div>
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center text-zinc-500 gap-4">
                    <flux:icon icon="book-open" class="size-12 opacity-20" />
                    <p>{{ __('kanban-board::messages.select_or_create_page') }}</p>
                    <flux:button wire:click="createPage" variant="primary">{{ __('kanban-board::messages.create_first_page') }}</flux:button>
                </div>
            @endif
        </div>
    </div>
</div>
