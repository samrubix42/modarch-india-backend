<div
    x-data="{ deleteOpen: false }"
    x-on:open-delete-modal.window="deleteOpen = true"
    x-on:close-delete-modal.window="deleteOpen = false"
    x-cloak
>
    <template x-teleport="body">
        <div x-show="deleteOpen" class="fixed inset-0 z-95 flex items-center justify-center px-4">
            <div @click="deleteOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

            <div
                x-show="deleteOpen"
                x-transition
                x-trap.inert.noscroll="deleteOpen"
                class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <h3 class="text-lg font-semibold text-slate-900">Delete Category</h3>
                <p class="mt-2 text-sm text-slate-600">This action cannot be undone. Are you sure you want to delete this category?</p>

                <div class="mt-6 flex justify-end gap-3">
                    <button @click="deleteOpen=false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                    <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete</button>
                </div>
            </div>
        </div>
    </template>
</div>
