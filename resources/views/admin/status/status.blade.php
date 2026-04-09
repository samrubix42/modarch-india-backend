<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Project Status Management</h1>
            <p class="mt-1 text-sm text-slate-500">Manage project statuses, order, and visibility.</p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal'); $wire.resetForm()"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-800 sm:w-auto">
            <i class="ri-add-line text-base"></i>
            Add Status
        </button>
    </div>

    <div class="relative w-full sm:w-96">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="ri-search-line"></i>
        </span>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search statuses..."
            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Slug</th>
                        <th class="px-6 py-4 text-left">Sort Order</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody wire:sort="sortItem" class="divide-y divide-slate-100">
                    @forelse ($statuses as $status)
                        <tr wire:key="status-{{ $status->id }}" wire:sort:item="{{ $status->id }}" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2">
                                    <button type="button" wire:sort:handle class="mt-0.5 cursor-grab rounded-md border border-slate-200 p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 active:cursor-grabbing" title="Drag to reorder">
                                        <i class="ri-draggable"></i>
                                    </button>
                                    <p class="font-medium py-2 text-slate-900">{{ $status->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $status->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $status->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div x-data="{ switchOn: {{ $status->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="switchOn = !switchOn; $wire.toggleStatus({{ $status->id }})"
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
                                    <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $status->id }})" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-200">Edit</button>
                                    <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $status->id }})" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-100">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">No statuses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:sort="sortItem" class="space-y-3 p-3 md:hidden">
            @forelse ($statuses as $status)
                <article wire:key="status-mobile-{{ $status->id }}" wire:sort:item="{{ $status->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-slate-900">{{ $status->name }}</p>
                            <p class="mt-1 truncate font-mono text-xs text-slate-500">{{ $status->slug }}</p>
                        </div>
                        <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $status->id }})" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700">Edit</button>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" wire:sort:handle class="cursor-grab rounded-md border border-slate-200 p-1 text-slate-500 active:cursor-grabbing">
                                <i class="ri-draggable text-[13px]"></i>
                            </button>
                            <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $status->sort_order }}</span>
                        </div>

                        <div x-data="{ switchOn: {{ $status->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                            <button
                                type="button"
                                @click="switchOn = !switchOn; $wire.toggleStatus({{ $status->id }})"
                                :class="switchOn ? 'bg-emerald-700' : 'bg-neutral-200'"
                                class="relative inline-flex h-5 w-9 rounded-full p-0.5 transition-colors duration-200"
                            >
                                <span :class="switchOn ? 'translate-x-4' : 'translate-x-0'" class="h-4 w-4 rounded-full bg-white transition-transform duration-200"></span>
                            </button>
                            <span :class="switchOn ? 'text-emerald-700' : 'text-gray-400'" class="text-[11px] font-medium" x-text="switchOn ? 'Active' : 'Inactive'"></span>
                        </div>

                        <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $status->id }})" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700">Delete</button>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                    No statuses found.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-100 px-4 py-3 text-xs text-slate-500 sm:px-6">
            {{ count($statuses) }} statuses
        </div>
    </div>

    <div x-data="{ modalOpen: false }" x-on:open-modal.window="modalOpen = true" x-on:close-modal.window="modalOpen = false" x-cloak>
        <template x-teleport="body">
            <div x-show="modalOpen" class="fixed inset-0 z-90 flex items-center justify-center p-4">
                <div @click="modalOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

                <div x-show="modalOpen" x-transition x-trap.inert.noscroll="modalOpen" class="relative w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $statusId ? 'Edit Status' : 'Add Status' }}</h3>
                            <p class="text-xs text-slate-500">Fill in status details and save.</p>
                        </div>
                        <button @click="modalOpen=false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <i class="ri-close-line text-lg"></i>
                        </button>
                    </div>

                    <div class="space-y-5 px-6 py-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Name</label>
                                <input wire:model.live="name" placeholder="e.g. Ongoing" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Slug</label>
                                <input wire:model.live="slug" placeholder="e.g. ongoing" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Sort Order</label>
                                <input type="number" min="0" wire:model="sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    Active Status
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button @click="modalOpen=false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Cancel</button>
                        <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-800 disabled:opacity-60">
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
                <div @click="deleteOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

                <div x-show="deleteOpen" x-transition x-trap.inert.noscroll="deleteOpen" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="text-lg font-semibold text-slate-900">Delete Status</h3>
                    <p class="mt-2 text-sm text-slate-600">This action cannot be undone. Are you sure you want to delete this status?</p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="deleteOpen=false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>