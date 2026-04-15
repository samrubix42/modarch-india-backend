
@php($sliders = $this->slidersPaginator())

<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.projects') }}" wire:navigate class="inline-flex items-center gap-1 rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100">
                    <i class="ri-arrow-left-line"></i>
                    Back to Projects
                </a>
            </div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Project Slider Content</h1>
            <p class="mt-1 text-sm text-slate-500">Project: <span class="font-medium text-slate-700">{{ $project->project_name }}</span></p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal'); $wire.openCreateModal()"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-800 sm:w-auto">
            <i class="ri-add-line text-base"></i>
            Add Slider Item
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
                placeholder="Search by type or description..."
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
                <thead class="bg-white text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Type</th>
                        <th class="px-6 py-4 text-left">Content</th>
                        <th class="px-6 py-4 text-left">Width</th>
                        <th class="px-6 py-4 text-left">Order</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody wire:sort="sortItem" class="divide-y divide-slate-100">
                    @forelse ($sliders as $slider)
                        <tr wire:key="slider-{{ $slider->id }}" wire:sort:item="{{ $slider->id }}" class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:sort:handle class="cursor-grab rounded-md border border-slate-200 p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 active:cursor-grabbing" title="Drag to reorder">
                                        <i class="ri-draggable"></i>
                                    </button>
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize {{ $slider->type === 'image' ? 'bg-emerald-100 text-emerald-700' : ($slider->type === 'video' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $slider->type }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($slider->type === 'image' && $slider->image)
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="Slider image" class="h-12 w-20 rounded-md object-cover ring-1 ring-slate-200">
                                @elseif ($slider->type === 'video' && $slider->video)
                                    <video class="h-12 w-20 rounded-md object-cover ring-1 ring-slate-200" muted>
                                        <source src="{{ asset('storage/' . $slider->video) }}">
                                    </video>
                                @elseif ($slider->type === 'description')
                                    <p class="line-clamp-2 max-w-xs text-xs text-slate-600">{{ $slider->description }}</p>
                                @else
                                    <p class="text-xs text-slate-400">No content</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $slider->width ?: '100' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $slider->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $slider->id }})" class="rounded-md bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $slider->id }})" class="rounded-md bg-rose-50 p-2 text-rose-700 transition hover:bg-rose-100" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-500">No slider items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:sort="sortItem" class="space-y-3 p-3 lg:hidden">
            @forelse ($sliders as $slider)
                <article wire:key="slider-mobile-{{ $slider->id }}" wire:sort:item="{{ $slider->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" wire:sort:handle class="cursor-grab rounded-md border border-slate-200 p-1 text-slate-500 active:cursor-grabbing">
                                <i class="ri-draggable text-[13px]"></i>
                            </button>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize {{ $slider->type === 'image' ? 'bg-emerald-100 text-emerald-700' : ($slider->type === 'video' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $slider->type }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2">
                            <button type="button" @click="$dispatch('open-modal'); $wire.openEditModal({{ $slider->id }})" class="rounded-md bg-slate-100 p-1.5 text-slate-700">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $slider->id }})" class="rounded-md bg-rose-50 p-1.5 text-rose-700">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        @if ($slider->type === 'image' && $slider->image)
                            <img src="{{ asset('storage/' . $slider->image) }}" alt="Slider image" class="h-20 w-full rounded-md object-cover ring-1 ring-slate-200">
                        @elseif ($slider->type === 'video' && $slider->video)
                            <video controls class="h-32 w-full rounded-md object-cover ring-1 ring-slate-200">
                                <source src="{{ asset('storage/' . $slider->video) }}">
                            </video>
                        @elseif ($slider->type === 'description')
                            <p class="rounded-md bg-slate-50 p-3 text-sm text-slate-600">{{ $slider->description }}</p>
                        @else
                            <p class="text-xs text-slate-400">No content</p>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-between text-xs">
                        <p class="text-slate-500">Width: <span class="font-medium text-slate-700">{{ $slider->width ?: '100' }}</span></p>
                        <p class="text-slate-500">Order: <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2 py-1 font-medium text-slate-700">{{ $slider->sort_order }}</span></p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                    No slider items found.
                </div>
            @endforelse
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="text-xs text-slate-500">Showing {{ $sliders->count() }} of {{ $sliders->total() }} slider items</p>

            @if ($sliders->lastPage() > 1)
                <div class="flex items-center gap-1">
                    <button type="button" wire:click="previousPage" @disabled($page <= 1) class="rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Prev</button>
                    @for ($i = 1; $i <= $sliders->lastPage(); $i++)
                        <button type="button" wire:click="gotoPage({{ $i }})" class="rounded-md px-2 py-1 text-xs {{ $page === $i ? 'bg-emerald-700 text-white' : 'border border-slate-300 text-slate-600 hover:bg-slate-50' }}">{{ $i }}</button>
                    @endfor
                    <button type="button" wire:click="nextPage" @disabled($page >= $sliders->lastPage()) class="rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
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
                            <h3 class="text-lg font-semibold text-slate-900">{{ $sliderId ? 'Edit Slider Item' : 'Add Slider Item' }}</h3>
                            <p class="text-xs text-slate-500">Add project slider content and set type-specific data.</p>
                        </div>
                        <button @click="modalOpen = false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <i class="ri-close-line text-lg"></i>
                        </button>
                    </div>

                    <div class="max-h-[75vh] space-y-5 overflow-y-auto px-6 py-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Type</label>
                                <select wire:model.live="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                    @foreach ($this->typeOptions() as $typeOption)
                                        <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Width</label>
                                <input wire:model.live="width" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="100">
                                @error('width') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Sort Order</label>
                                <input type="number" min="1" wire:model.live="sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if ($type === 'image')
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Image Upload (Multiple)</label>
                                <input type="file" multiple wire:model="image_files" accept="image/*" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                                <p class="mt-1 text-[11px] text-slate-500">Files are inserted in the same order as selected.</p>
                                <p wire:loading wire:target="image_files" class="mt-1 text-xs text-emerald-700">Uploading image files...</p>
                                @error('image_files') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                @error('image_files.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                                @if (!empty($image_files))
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($image_files as $index => $file)
                                            <div class="relative">
                                                <img src="{{ $file->temporaryUrl() }}" alt="Image preview {{ $index + 1 }}" class="h-20 w-24 rounded-md object-cover ring-1 ring-slate-200">
                                                <button type="button" wire:click="removeImageFile({{ $index }})" class="absolute -top-1 -left-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-white transition hover:bg-rose-700" title="Remove image">
                                                    <i class="ri-close-line text-[11px]"></i>
                                                </button>
                                                <span class="absolute -top-1 -right-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-900 text-[10px] font-semibold text-white">{{ $index + 1 }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif ($existing_image)
                                    <img src="{{ asset('storage/' . $existing_image) }}" alt="Current image" class="mt-2 h-28 w-44 rounded-md object-cover ring-1 ring-slate-200">
                                @endif
                            </div>
                        @endif

                        @if ($type === 'video')
                            <div>
                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Video Upload</label>
                                <input type="file" wire:model="video_file" accept="video/mp4,video/quicktime,video/webm" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                                <p wire:loading wire:target="video_file" class="mt-1 text-xs text-emerald-700">Uploading video...</p>
                                @error('video_file') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                                @if ($video_file)
                                    <video controls class="mt-2 h-32 w-full max-w-md rounded-md object-cover ring-1 ring-slate-200">
                                        <source src="{{ $video_file->temporaryUrl() }}">
                                    </video>
                                @elseif ($existing_video)
                                    <video controls class="mt-2 h-32 w-full max-w-md rounded-md object-cover ring-1 ring-slate-200">
                                        <source src="{{ asset('storage/' . $existing_video) }}">
                                    </video>
                                @endif
                            </div>
                        @endif

                        <div @class(['hidden' => $type !== 'description'])>
                            <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500">Description Content</label>

                            <div
                                wire:ignore
                                x-data="projectSliderDescriptionTinyMce(@entangle('description'))"
                                x-on:open-modal.window="boot()"
                                x-on:tinymce-set-project-slider-description.window="setContent($event.detail.content || '')"
                            >
                                <textarea x-ref="editor" data-project-slider-description-editor="true"></textarea>
                            </div>

                            @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button @click="modalOpen = false" type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Cancel</button>
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save,image_files,video_file" class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-800 disabled:opacity-60">
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
                    <h3 class="text-lg font-semibold text-slate-900">Delete Slider Item</h3>
                    <p class="mt-2 text-sm text-slate-600">This action cannot be undone. Are you sure you want to delete this slider item?</p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="deleteOpen = false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

@once
    <script src="https://cdn.tiny.cloud/1/pvxf2rey6dhbd0zfoep9pxag4n66tqcoa74t54qq0aybqjbs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        window.addEventListener('load', () => {
            if (typeof tinymce !== 'undefined') {
                return;
            }

            const fallback = document.createElement('script');
            fallback.src = "{{ asset('tinymce/tinymce.min.js') }}";
            document.head.appendChild(fallback);
        });
    </script>
    <style>
        .tox-tinymce-aux,
        .tox-dialog,
        .tox-menu,
        .tox-collection,
        .tox-pop,
        .mce-container,
        .moxman-window {
            z-index: 12000 !important;
        }
    </style>
@endonce

<script>
    (() => {
        if (window.__projectSliderDescriptionTinyMceRegistered) {
            return;
        }

        window.__projectSliderDescriptionTinyMceRegistered = true;

        window.projectSliderDescriptionTinyMce = function (contentModel) {
            return {
                content: contentModel,
                editor: null,
                editorId: null,
                isSyncingFromEditor: false,

                init() {
                    this.$watch('content', (value) => {
                        if (this.isSyncingFromEditor || !this.editor || !this.editor.initialized) {
                            return;
                        }

                        if (this.editor.hasFocus()) {
                            return;
                        }

                        const nextContent = value || '';
                        if (this.editor.getContent() !== nextContent) {
                            this.editor.setContent(nextContent);
                        }
                    });

                    this.boot();
                },

                boot() {
                    this.$nextTick(() => {
                        this.waitAndInit(0);
                    });
                },

                waitAndInit(attempt) {
                    if (typeof tinymce === 'undefined') {
                        if (attempt < 20) {
                            setTimeout(() => this.waitAndInit(attempt + 1), 100);
                        }
                        return;
                    }

                    this.initializeEditor();
                },

                initializeEditor() {
                    if (!this.$refs.editor) {
                        return;
                    }

                    if (!this.editorId) {
                        this.editorId = `project-slider-description-editor-${Math.random().toString(36).slice(2, 10)}`;
                    }

                    this.$refs.editor.id = this.editorId;

                    const existing = tinymce.get(this.editorId);
                    if (existing) {
                        existing.remove();
                    }

                    tinymce.init({
                        selector: `#${this.editorId}`,
                        menubar: true,
                        branding: false,
                        promotion: false,
                        readonly: false,
                        height: 320,
                        plugins: 'autolink advlist lists link anchor image table code codesample searchreplace wordcount charmap',
                        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor | image table | codesample code | removeformat',
                        toolbar_mode: 'sliding',
                        block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Preformatted=pre',
                        codesample_global_prismjs: false,
                        codesample_languages: [
                            { text: 'HTML/XML', value: 'markup' },
                            { text: 'CSS', value: 'css' },
                            { text: 'JavaScript', value: 'javascript' },
                            { text: 'PHP', value: 'php' },
                            { text: 'Bash', value: 'bash' },
                            { text: 'JSON', value: 'json' }
                        ],
                        content_style: 'pre { background:#0f172a; color:#e2e8f0; padding:12px; border-radius:8px; overflow:auto; } code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }',
                        convert_urls: true,
                        relative_urls: false,
                        remove_script_host: false,
                        link_assume_external_targets: true,
                        default_link_target: '_blank',
                        setup: (editor) => {
                            this.editor = editor;

                            editor.on('init', () => {
                                editor.setContent(this.content || '');
                            });

                            editor.on('change keyup undo redo input SetContent', () => {
                                this.isSyncingFromEditor = true;
                                this.content = editor.getContent();
                                this.$nextTick(() => {
                                    this.isSyncingFromEditor = false;
                                });
                            });
                        },
                    });
                },

                setContent(value) {
                    this.content = value || '';

                    if (this.editor && this.editor.initialized && this.editor.getContent() !== this.content) {
                        this.editor.setContent(this.content);
                    }
                },

                destroy() {
                    if (this.editor) {
                        this.editor.remove();
                        this.editor = null;
                    }
                }
            };
        };

        document.addEventListener('livewire:navigating', () => {
            if (typeof tinymce === 'undefined') {
                return;
            }

            tinymce.editors
                .filter((editor) => editor.getElement()?.hasAttribute('data-project-slider-description-editor'))
                .forEach((editor) => editor.remove());
        });
    })();
</script>