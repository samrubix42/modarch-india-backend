<div
    x-data="{ modalOpen: false }"
    x-on:open-modal.window="modalOpen = true"
    x-on:close-modal.window="modalOpen = false"
    x-cloak
>
    <template x-teleport="body">
        <div x-show="modalOpen" class="fixed inset-0 z-90 flex items-center justify-center p-4">
            <div @click="modalOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

            <div
                x-show="modalOpen"
                x-transition
                x-trap.inert.noscroll="modalOpen"
                class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl"
            >
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $categoryId ? 'Edit Category' : 'Add Category' }}</h3>
                        <p class="text-xs text-slate-500">Fill in category details and save.</p>
                    </div>
                    <button @click="modalOpen=false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <div class="space-y-5 px-6 py-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Name</label>
                            <input wire:model.live="name" placeholder="e.g. Residential Projects" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Slug</label>
                            <input wire:model.live="slug" placeholder="e.g. residential-projects" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            @error('slug')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Sort Order</label>
                            <input type="number" min="0" wire:model="sort_order" placeholder="0" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            @error('sort_order')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Meta Title</label>
                            <input wire:model.live="meta_title" placeholder="SEO title for this category" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            @error('meta_title')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Meta Description</label>
                            <textarea rows="3" wire:model.live="meta_description" placeholder="Short description for search engines" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"></textarea>
                            @error('meta_description')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Active Category
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button @click="modalOpen=false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Cancel</button>
                    <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center rounded-xl bg-emerald-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Save</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
