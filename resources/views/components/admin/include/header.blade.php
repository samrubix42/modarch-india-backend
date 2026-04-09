<?php

use Livewire\Component;

new class extends Component
{
    public string $pageTitle = 'Dashboard';

    public function mount(?string $title = null): void
    {
        $segment = request()->segment(2) ?: request()->segment(1);
        $fallback = $segment ? str($segment)->headline()->toString() : 'Dashboard';

        $this->pageTitle = $title ?: $fallback;
    }
};
?>

@php
    $user = auth()->user();
    $displayName = $user?->name ?? 'Admin User';
    $displayEmail = $user?->email ?? 'admin@modarch.test';
    $initials = str($displayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => str($part)->substr(0, 1)->upper())
        ->implode('');
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <i class="ri-menu-line text-xl"></i>
            </button>

            <div>
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500">Workspace</p>
                <h2 class="text-xl font-semibold text-slate-900">{{ $pageTitle }}</h2>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <label class="relative hidden md:block">
                <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    placeholder="Search..."
                    class="h-11 w-56 rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-teal-400 focus:bg-white"
                >
            </label>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                aria-label="Notifications"
            >
                <i class="ri-notification-3-line text-xl"></i>
            </button>

            <div class="relative" x-data="{ profileMenu: false }">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-left transition hover:bg-slate-50"
                    @click="profileMenu = !profileMenu"
                >
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-teal-100 text-sm font-semibold text-teal-700">{{ $initials }}</span>
                    <span class="hidden sm:block">
                        <span class="block text-xs text-slate-500">Administrator</span>
                        <span class="block text-sm font-semibold text-slate-900">{{ $displayName }}</span>
                    </span>
                    <i class="ri-arrow-down-s-line text-lg text-slate-500"></i>
                </button>

                <div
                    x-show="profileMenu"
                    x-transition.origin.top.right
                    @click.outside="profileMenu = false"
                    class="absolute right-0 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg shadow-slate-900/10"
                >
                    <div class="border-b border-slate-100 px-3 py-2">
                        <p class="text-sm font-semibold text-slate-800">{{ $displayName }}</p>
                        <p class="text-xs text-slate-500">{{ $displayEmail }}</p>
                    </div>

                    <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                        <i class="ri-user-settings-line"></i>
                        Profile
                    </a>
                    <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                        <i class="ri-equalizer-3-line"></i>
                        Preferences
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-rose-600 transition hover:bg-rose-50">
                            <i class="ri-logout-box-r-line"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>