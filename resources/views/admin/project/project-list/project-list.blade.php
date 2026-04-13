
@php($projects = $this->projects)

<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Project Management</h1>
            <p class="mt-1 text-sm text-slate-500">Manage projects, upload media, and open each project's slider content.</p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal'); $wire.resetForm()"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-800 sm:w-auto">
            <i class="ri-add-line text-base"></i>
            Add Project
        </button>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="ri-search-line"></i>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by project, client, or slug..."
                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-medium uppercase tracking-wide text-slate-500">Rows</label>
            <select wire:model.live="perPage" class="rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Thumbnail</th>
                        <th class="px-6 py-4 text-left">Title</th>
                        <th class="px-6 py-4 text-left">Main Image</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Active</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($projects as $project)
                        <tr wire:key="project-{{ $project->id }}" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                @if ($project->project_thumbnail)
                                    <img src="{{ asset('storage/' . $project->project_thumbnail) }}" alt="{{ $project->project_name }} thumbnail" class="h-14 w-20 rounded-lg object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-14 w-20 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[10px] text-slate-400">No image</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $project->project_name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $project->client_name }}</p>
                                    <p class="mt-1 font-mono text-[11px] text-slate-400">{{ $project->slug }}</p>
                                    @if ($project->categories->isNotEmpty())
                                        <p class="mt-1 text-[11px] text-slate-500">{{ $project->categories->pluck('name')->join(', ') }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($project->project_main_image)
                                    <img src="{{ asset('storage/' . $project->project_main_image) }}" alt="{{ $project->project_name }} main image" class="h-14 w-24 rounded-lg object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-14 w-24 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[10px] text-slate-400">No image</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $project->status?->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div x-data="{ switchOn: {{ $project->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="switchOn = !switchOn; $wire.toggleStatus({{ $project->id }})"
                                        :class="switchOn ? 'bg-emerald-700' : 'bg-neutral-200'"
                                        class="relative inline-flex h-5 w-9 rounded-full p-0.5 transition-colors duration-200"
                                    >
                                        <span :class="switchOn ? 'translate-x-4' : 'translate-x-0'" class="h-4 w-4 rounded-full bg-white transition-transform duration-200"></span>
                                    </button>
                                    <span :class="switchOn ? 'text-emerald-700' : 'text-gray-400'" class="text-[11px] font-medium" x-text="switchOn ? 'Active' : 'Inactive'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.project-sliders', $project) }}" wire:navigate class="rounded-md bg-blue-50 p-2 text-blue-700 transition hover:bg-blue-100" title="Open Slider">
                                        <i class="ri-slideshow-line"></i>
                                    </a>
                                    <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $project->id }})" class="rounded-md bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $project->id }})" class="rounded-md bg-rose-50 p-2 text-rose-700 transition hover:bg-rose-100" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-400">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-3 lg:hidden">
            @forelse ($projects as $project)
                <article wire:key="project-mobile-{{ $project->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-slate-900">{{ $project->project_name }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $project->client_name }}</p>
                        </div>
                    </div>

                    <div class="mt-3 text-xs">
                        <p class="text-slate-400">Status</p>
                        <p class="mt-1 text-slate-700">{{ $project->status?->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <div>
                            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Thumb</p>
                            @if ($project->project_thumbnail)
                                <img src="{{ asset('storage/' . $project->project_thumbnail) }}" alt="{{ $project->project_name }} thumbnail" class="h-14 w-16 rounded-md object-cover ring-1 ring-slate-200">
                            @else
                                <div class="flex h-14 w-16 items-center justify-center rounded-md border border-dashed border-slate-300 bg-slate-50 text-[10px] text-slate-400">No image</div>
                            @endif
                        </div>

                        <div class="mx-auto">
                            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Main</p>
                            @if ($project->project_main_image)
                                <img src="{{ asset('storage/' . $project->project_main_image) }}" alt="{{ $project->project_name }} image" class="h-14 w-20 rounded-md object-cover ring-1 ring-slate-200">
                            @else
                                <div class="flex h-14 w-20 items-center justify-center rounded-md border border-dashed border-slate-300 bg-slate-50 text-[10px] text-slate-400">No image</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1">
                        @forelse ($project->categories as $category)
                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-700">{{ $category->name }}</span>
                        @empty
                            <span class="text-[11px] text-slate-400">No category selected</span>
                        @endforelse
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div x-data="{ switchOn: {{ $project->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                            <button
                                type="button"
                                @click="switchOn = !switchOn; $wire.toggleStatus({{ $project->id }})"
                                :class="switchOn ? 'bg-emerald-700' : 'bg-neutral-200'"
                                class="relative inline-flex h-5 w-9 rounded-full p-0.5 transition-colors duration-200"
                            >
                                <span :class="switchOn ? 'translate-x-4' : 'translate-x-0'" class="h-4 w-4 rounded-full bg-white transition-transform duration-200"></span>
                            </button>
                            <span :class="switchOn ? 'text-emerald-700' : 'text-gray-400'" class="text-[11px] font-medium" x-text="switchOn ? 'Active' : 'Inactive'"></span>
                        </div>

                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.project-sliders', $project) }}" wire:navigate class="rounded-md bg-blue-50 p-2 text-blue-700" title="Open Slider">
                                <i class="ri-slideshow-line"></i>
                            </a>
                            <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $project->id }})" class="rounded-md bg-slate-100 p-2 text-slate-700">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $project->id }})" class="rounded-md bg-rose-50 p-2 text-rose-700">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                    No projects found.
                </div>
            @endforelse
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="text-xs text-slate-500">Showing {{ $projects->count() }} of {{ $projects->total() }} projects</p>

            @if ($projects->lastPage() > 1)
                <div>
                    {{ $projects->links('vendor.livewire.tailwind') }}
                </div>
            @endif
        </div>
    </div>

    <div x-data="{ modalOpen: false }" x-on:open-modal.window="modalOpen = true" x-on:close-modal.window="modalOpen = false" x-cloak>
        <template x-teleport="body">
            <div x-show="modalOpen" class="fixed inset-0 z-90 flex items-center justify-center p-4">
                <div @click="modalOpen = false" class="absolute inset-0 bg-slate-900/50"></div>

                <div x-show="modalOpen" x-transition x-trap.inert.noscroll="modalOpen" class="relative w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $projectId ? 'Edit Project' : 'Add Project' }}</h3>
                            <p class="text-xs text-slate-500">Manage project details, media, and status.</p>
                        </div>
                        <button @click="modalOpen = false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <i class="ri-close-line text-lg"></i>
                        </button>
                    </div>

                    <div class="max-h-[75vh] space-y-5 overflow-y-auto px-6 py-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Client Name</label>
                                <input wire:model.live="client_name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Client name">
                                @error('client_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Project Name</label>
                                <input wire:model.live="project_name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Project name">
                                @error('project_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Project Address</label>
                                <input wire:model.live="project_address" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Optional">
                                @error('project_address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Slug</label>
                                <input wire:model.live="slug" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="project-slug">
                                @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Site Area</label>
                                <input wire:model.live="site_area" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="e.g. 5000 sq.ft">
                                @error('site_area') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Built Up Area</label>
                                <input wire:model.live="built_up_area" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Optional">
                                @error('built_up_area') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Tag</label>
                                <select wire:model.live="tag_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                    <option value="">Select Tag</option>
                                    @foreach ($this->tagOptions() as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                @error('tag_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Status</label>
                                <select wire:model.live="project_status_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                    <option value="">Select Status</option>
                                    @foreach ($this->statusOptions() as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_status_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Project Categories</label>
                                <div class="rounded-xl border border-slate-300 bg-slate-50 p-3">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($this->categoryOptions() as $category)
                                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50">
                                                <input type="checkbox" value="{{ $category->id }}" wire:model.live="selected_category_ids" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                {{ $category->name }}
                                            </label>
                                        @empty
                                            <p class="text-xs text-slate-500">No active categories found.</p>
                                        @endforelse
                                    </div>
                                </div>
                                @error('selected_category_ids') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                @error('selected_category_ids.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Thumbnail Image</label>
                                <div class="rounded-xl border border-slate-300 bg-white p-3">
                                    <input type="file" wire:model="project_thumbnail" accept="image/*" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-emerald-800 hover:file:bg-emerald-200">
                                    <p class="mt-2 text-[11px] text-slate-500">Recommended ratio: 4:3 for list cards.</p>
                                </div>
                                <p wire:loading wire:target="project_thumbnail" class="mt-1 text-xs text-emerald-700">Uploading thumbnail...</p>
                                @error('project_thumbnail') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                                @if ($project_thumbnail)
                                    <img src="{{ $project_thumbnail->temporaryUrl() }}" alt="Thumbnail preview" class="mt-2 h-24 w-36 rounded-lg object-cover ring-1 ring-slate-200">
                                @elseif ($existing_thumbnail)
                                    <img src="{{ asset('storage/' . $existing_thumbnail) }}" alt="Current thumbnail" class="mt-2 h-24 w-36 rounded-lg object-cover ring-1 ring-slate-200">
                                @endif
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Main Image</label>
                                <div class="rounded-xl border border-slate-300 bg-white p-3">
                                    <input type="file" wire:model="project_main_image" accept="image/*" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-sky-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-sky-800 hover:file:bg-sky-200">
                                    <p class="mt-2 text-[11px] text-slate-500">Recommended ratio: 16:9 for detail hero.</p>
                                </div>
                                <p wire:loading wire:target="project_main_image" class="mt-1 text-xs text-emerald-700">Uploading main image...</p>
                                @error('project_main_image') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                                @if ($project_main_image)
                                    <img src="{{ $project_main_image->temporaryUrl() }}" alt="Main image preview" class="mt-2 h-24 w-36 rounded-lg object-cover ring-1 ring-slate-200">
                                @elseif ($existing_main_image)
                                    <img src="{{ asset('storage/' . $existing_main_image) }}" alt="Current main image" class="mt-2 h-24 w-36 rounded-lg object-cover ring-1 ring-slate-200">
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" wire:model.live="is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Active Project
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button @click="modalOpen = false" type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Cancel</button>
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save,project_thumbnail,project_main_image" class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-800 disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div x-data="{ deleteOpen: false }" x-on:open-delete-modal.window="deleteOpen = true" x-on:close-delete-modal.window="deleteOpen = false" x-cloak>
        <template x-teleport="body">
            <div x-show="deleteOpen" class="fixed inset-0 z-95 flex items-center justify-center px-4">
                <div @click="deleteOpen = false" class="absolute inset-0 bg-slate-900/50"></div>

                <div x-show="deleteOpen" x-transition x-trap.inert.noscroll="deleteOpen" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="text-lg font-semibold text-slate-900">Delete Project</h3>
                    <p class="mt-2 text-sm text-slate-600">This action cannot be undone. Are you sure you want to delete this project?</p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="deleteOpen = false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>