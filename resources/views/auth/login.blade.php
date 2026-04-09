<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component {
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('admin.dashboard', navigate: true);
        }
    }

    public function authenticate(): void
    {
        $credentials = $this->validate();

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', 'The provided credentials do not match our records.');

            return;
        }

        request()->session()->regenerate();

        $this->redirectIntended(default: route('admin.dashboard'), navigate: true);
    }
};
?>

<div class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-2xl border border-emerald-100 bg-white p-8 shadow-lg shadow-emerald-900/5">
        <div class="mb-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-600">Modarch Backend</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Sign In</h1>
            <p class="mt-1 text-sm text-slate-500">Use your admin account to continue</p>
        </div>

        <form wire:submit="authenticate" class="space-y-4">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                <div class="relative">
                    <i class="ri-mail-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        id="email"
                        wire:model="email"
                        type="email"
                        required
                        autofocus
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        placeholder="you@example.com"
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <i class="ri-lock-password-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        id="password"
                        wire:model="password"
                        type="password"
                        required
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        placeholder="Enter your password"
                    >
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input wire:model="remember" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Remember me
            </label>

            <button
                type="submit"
                class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-emerald-600 px-4 font-semibold text-white transition hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-200"
                wire:loading.attr="disabled"
            >
                <i class="ri-login-circle-line mr-2 text-lg"></i>
                <span wire:loading.remove>Sign In</span>
                <span wire:loading>Signing In...</span>
            </button>
        </form>
    </div>
</div>