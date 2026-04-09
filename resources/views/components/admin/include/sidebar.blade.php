<?php

use App\Views\Builders\AdminSidebar;
use Livewire\Component;

new class extends Component
{
    //
};
?>

@php
    $menu = AdminSidebar::menu(auth()->user())->get();

    $openGroups = $menu
        ->filter(fn ($item): bool => $item->hasSubmenu)
        ->mapWithKeys(fn ($item): array => [$item->key => (bool) $item->open])
        ->all();
@endphp

<div x-data="{ openGroups: @js($openGroups), toggleGroup(key) { this.openGroups[key] = !this.openGroups[key]; } }">
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-30 bg-slate-900/45 backdrop-blur-[1px] lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 z-40 w-72 border-r border-slate-200/80 bg-white shadow-xl shadow-slate-900/10 transition-transform duration-300 ease-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        <div class="h-1 w-full bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600"></div>

        <div class="flex h-20 items-center justify-between border-b border-slate-100 px-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-600">Modarch</p>
                <h1 class="text-lg font-semibold text-slate-900">Admin Console</h1>
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 lg:hidden"
                @click="sidebarOpen = false"
                aria-label="Close sidebar"
            >
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <nav class="h-[calc(100vh-5.25rem)] overflow-y-auto px-3 py-4">
            <ul class="space-y-1.5">
                @foreach ($menu as $item)
                    <li>
                        @if ($item->hasSubmenu)
                            <button
                                type="button"
                                class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition"
                                :class="{{ $item->active ? 'true' : 'false' }} ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                @click="toggleGroup('{{ $item->key }}')"
                            >
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-base"
                                      :class="{{ $item->active ? 'true' : 'false' }} ? 'border-emerald-200 text-emerald-700' : 'text-slate-500 group-hover:text-slate-800'">
                                    <i class="{{ $item->icon }}"></i>
                                </span>
                                <span class="flex-1">{{ $item->title }}</span>
                                <i class="ri-arrow-down-s-line text-lg transition"
                                   :class="openGroups['{{ $item->key }}'] ? 'rotate-180' : ''"></i>
                            </button>

                            <ul
                                x-show="openGroups['{{ $item->key }}']"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                x-cloak
                                class="ml-6 mt-1.5 space-y-1 border-l border-slate-200 pl-4"
                            >
                                @foreach ($item->submenu as $child)
                                    <li>
                                        <a
                                            href="{{ $child->url }}"
                                            wire:navigate
                                            class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition"
                                            @class([
                                                'bg-emerald-100/80 text-emerald-800' => $child->active ?? false,
                                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! ($child->active ?? false),
                                            ])
                                        >
                                            <i class="ri-checkbox-blank-circle-line text-[10px]"
                                               @class([
                                                   'text-emerald-700' => $child->active ?? false,
                                                   'text-slate-400 group-hover:text-slate-700' => ! ($child->active ?? false),
                                               ])></i>
                                            <span>{{ $child->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a
                                href="{{ $item->url }}"
                                wire:navigate
                                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                                @class([
                                    'bg-emerald-50 text-emerald-700' => $item->active,
                                    'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $item->active,
                                ])
                            >
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-base"
                                      @class([
                                          'border-emerald-200 text-emerald-700' => $item->active,
                                          'text-slate-500 group-hover:text-slate-800' => ! $item->active,
                                      ])>
                                    <i class="{{ $item->icon }}"></i>
                                </span>
                                <span>{{ $item->title }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </aside>
</div>