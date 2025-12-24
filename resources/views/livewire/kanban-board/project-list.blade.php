<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('kanban-board::messages.project_list') }}</h1>
                <div>
                    <flux:modal.trigger name="add-project">
                        <flux:button>{{ __('kanban-board::messages.new_project') }}</flux:button>
                    </flux:modal.trigger>
                    <flux:modal name="add-project" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading
                                    size="lg">{{ $editMode ? __('kanban-board::messages.edit_project') : __('kanban-board::messages.new_project') }}</flux:heading>
                            </div>
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
                                <flux:select wire:model="user_id">
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
            </div>

            <!-- フィルターセクション -->
            <div class="mt-4 bg-white p-4 rounded-md shadow">
                <div class="flex flex-wrap gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('kanban-board::messages.search') }}</label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" placeholder="{{ __('kanban-board::messages.project_name') }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    </div>
                    <div class="flex items-end">
                        <button wire:click="resetFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('kanban-board::messages.reset') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sort('id')">
                                    ID
                                    @if ($sortBy === 'id')
                                        @if ($sortDirection === 'asc')
                                            <span>↑</span>
                                        @else
                                            <span>↓</span>
                                        @endif
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('kanban-board::messages.project_name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('kanban-board::messages.admin') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('kanban-board::messages.description') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sort('created_at')">
                                    {{ __('kanban-board::messages.created_at') }}
                                    @if ($sortBy === 'created_at')
                                        @if ($sortDirection === 'asc')
                                            <span>↑</span>
                                        @else
                                            <span>↓</span>
                                        @endif
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('kanban-board::messages.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($this->projects as $project)
                                <tr wire:key="task-{{ $project->id }}" >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" wire:click="openBoard({{ $project->id }})">{{ $project->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Lastdino\KanbanBoard\Helpers\UserDisplayHelper::getDisplayName($project->admin) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->created_at->format(config('kanban-board.datetime.formats.date')) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($project->user_id === auth()->id())
                                            <flux:tooltip content="{{ __('kanban-board::messages.settings') }}">
                                                <flux:button icon="cog-6-tooth" icon:variant="outline" variant="subtle"
                                                             wire:click="editProject({{ $project->id }})"/>
                                            </flux:tooltip>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('kanban-board::messages.no_projects') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $this->projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
