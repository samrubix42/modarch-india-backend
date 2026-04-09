<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Project Category Management</h1>
            <p class="mt-1 text-sm text-slate-500">Manage category details, visibility, and display order.</p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal'); $wire.resetForm()"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-800 sm:w-auto">
            <i class="ri-add-line text-base"></i>
            Add Category
        </button>
    </div>

    <div class="relative w-full sm:w-96">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="ri-search-line"></i>
        </span>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search categories..."
            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Category</th>
                        <th class="px-6 py-4 text-left">Slug</th>
                        <th class="px-6 py-4 text-left">Sort Order</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody wire:sort="sortItem" class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr wire:key="category-{{ $category->id }}" wire:sort:item="{{ $category->id }}" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2">
                                    <button type="button" wire:sort:handle class="mt-0.5 cursor-grab rounded-md border border-slate-200 p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 active:cursor-grabbing" title="Drag to reorder">
                                        <i class="ri-draggable"></i>
                                    </button>

                                    <div>
                                        <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                        @if ($category->meta_title)
                                            <p class="mt-1 text-xs text-slate-500">{{ $category->meta_title }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $category->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div x-data="{ switchOn: {{ $category->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="switchOn = !switchOn; $wire.toggleStatus({{ $category->id }})"
                                        :class="switchOn ? 'bg-emerald-700' : 'bg-neutral-200'"
                                        class="relative inline-flex h-5 w-9 rounded-full p-0.5 transition-colors duration-200"
                                    >
                                        <span
                                            :class="switchOn ? 'translate-x-4' : 'translate-x-0'"
                                            class="h-4 w-4 rounded-full bg-white transition-transform duration-200"
                                        ></span>
                                    </button>

                                    <span
                                        :class="switchOn ? 'text-emerald-700' : 'text-gray-400'"
                                        class="text-[11px] font-medium"
                                        x-text="switchOn ? 'Active' : 'Inactive'"
                                    ></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $category->id }})" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-200">Edit</button>
                                    <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $category->id }})" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-100">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:sort="sortItem" class="space-y-3 p-3 md:hidden">
            @forelse ($categories as $category)
            <article wire:key="category-mobile-{{ $category->id }}" wire:sort:item="{{ $category->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-slate-900">{{ $category->name }}</p>
                            @if ($category->meta_title)
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $category->meta_title }}</p>
                            @endif
                        </div>
                        <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $category->id }})" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700">Edit</button>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-slate-400">Slug</p>
                            <p class="mt-1 truncate font-mono text-slate-600">{{ $category->slug }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Sort Order</p>
                            <div class="mt-1 flex items-center gap-2">
                                <button type="button" wire:sort:handle class="cursor-grab rounded-md border border-slate-200 p-1 text-slate-500 active:cursor-grabbing">
                                    <i class="ri-draggable text-[13px]"></i>
                                </button>
                                <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $category->sort_order }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div x-data="{ switchOn: {{ $category->is_active ? 'true' : 'false' }} }" class="flex items-center space-x-2">
                            <button
                                type="button"
                                @click="switchOn = !switchOn; $wire.toggleStatus({{ $category->id }})"
                                :class="switchOn ? 'bg-emerald-700' : 'bg-neutral-200'"
                                class="relative inline-flex h-5 w-9 rounded-full p-0.5 transition-colors duration-200"
                            >
                                <span
                                    :class="switchOn ? 'translate-x-4' : 'translate-x-0'"
                                    class="h-4 w-4 rounded-full bg-white transition-transform duration-200"
                                ></span>
                            </button>
                            <span :class="switchOn ? 'text-emerald-700' : 'text-gray-400'" class="text-[11px] font-medium" x-text="switchOn ? 'Active' : 'Inactive'"></span>
                        </div>

                        <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $category->id }})" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700">Delete</button>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                    No categories found.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-100 px-4 py-3 text-xs text-slate-500 sm:px-6">
            {{ count($categories) }} categories
        </div>
    </div>

    @include('admin.project-category-list.partials.form-modal')
    @include('admin.project-category-list.partials.delete-modal')
</div>