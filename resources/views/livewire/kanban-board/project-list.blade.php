<div>
    <div class="flex justify-between items-center">
        <flux:heading size="xl">{{ __('kanban-board::messages.project_list') }}</flux:heading>

        <flux:modal.trigger name="add-project">
            <flux:button variant="primary" icon="plus">{{ __('kanban-board::messages.new_project') }}</flux:button>
        </flux:modal.trigger>

        <flux:modal name="add-project" class="md:w-96">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editMode ? __('kanban-board::messages.edit_project') : __('kanban-board::messages.new_project') }}</flux:heading>

                <flux:input
                    label="{{ __('kanban-board::messages.project_name') }}"
                    placeholder="{{ __('kanban-board::messages.project_name') }}"
                    wire:model="title"
                />

                <flux:textarea
                    label="{{ __('kanban-board::messages.description') }}"
                    placeholder="{{ __('kanban-board::messages.description_placeholder') }}"
                    wire:model="description"
                />

                <flux:field variant="inline">
                    <flux:label>{{ __('kanban-board::messages.private') }}</flux:label>
                    <flux:switch wire:model.live="is_private" />
                    <flux:error name="is_private" />
                </flux:field>

                @if($editMode)
                    <flux:select wire:model="user_id" label="{{ __('kanban-board::messages.admin') }}">
                        @foreach($users as $user)
                            <flux:select.option value="{{$user->id}}">{{ \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($user) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary"
                                 wire:click="{{ $editMode ? 'updateProject' : 'addProject' }}">{{ __('kanban-board::messages.save') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </div>

    <!-- フィルターセクション -->
    <div class="mt-4 flex flex-wrap items-end gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('kanban-board::messages.project_name') }}" label="{{ __('kanban-board::messages.search') }}" />

        <flux:button wire:click="resetFilters" icon="x-mark">
            {{ __('kanban-board::messages.reset') }}
        </flux:button>
    </div>

    <div class="mt-6">
        <flux:table :paginate="$this->projects">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')">ID</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.project_name') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.admin') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.description') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('kanban-board::messages.created_at') }}</flux:table.column>
                <flux:table.column>{{ __('kanban-board::messages.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->projects as $project)
                    <flux:table.row :key="'project-'.$project->id">
                        <flux:table.cell>{{ $project->id }}</flux:table.cell>
                        <flux:table.cell class="cursor-pointer" wire:click="openBoard({{ $project->id }})">
                            <flux:text variant="strong">{{ $project->title }}</flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($project->admin) }}</flux:table.cell>
                        <flux:table.cell>{{ $project->description }}</flux:table.cell>
                        <flux:table.cell>{{ $project->created_at->format(config('kanban-board.datetime.formats.date')) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($project->user_id === auth()->id())
                                <flux:tooltip content="{{ __('kanban-board::messages.settings') }}">
                                    <flux:button icon="cog-6-tooth" icon:variant="outline" variant="subtle"
                                                 wire:click="editProject({{ $project->id }})"/>
                                </flux:tooltip>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">{{ __('kanban-board::messages.no_projects') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
