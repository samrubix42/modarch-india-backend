<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public ?int $settingId = null;

    public string $address_1 = '';
    public string $address_2 = '';
    public string $phone_1 = '';
    public string $phone_2 = '';
    public string $email_1 = '';
    public string $email_2 = '';
    public string $instagram_url = '';
    public string $linkedin_url = '';
    public string $facebook_url = '';

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $setting = Setting::query()->first();

        if (! $setting) {
            return;
        }

        $this->settingId = $setting->id;
        $this->address_1 = (string) ($setting->address_1 ?? '');
        $this->address_2 = (string) ($setting->address_2 ?? '');
        $this->phone_1 = (string) ($setting->phone_1 ?? '');
        $this->phone_2 = (string) ($setting->phone_2 ?? '');
        $this->email_1 = (string) ($setting->email_1 ?? '');
        $this->email_2 = (string) ($setting->email_2 ?? '');
        $this->instagram_url = (string) ($setting->instagram_url ?? '');
        $this->linkedin_url = (string) ($setting->linkedin_url ?? '');
        $this->facebook_url = (string) ($setting->facebook_url ?? '');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'address_1' => ['nullable', 'string', 'max:500'],
            'address_2' => ['nullable', 'string', 'max:500'],
            'phone_1' => ['nullable', 'string', 'max:50'],
            'phone_2' => ['nullable', 'string', 'max:50'],
            'email_1' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
        ]);

        $setting = $this->settingId
            ? Setting::query()->find($this->settingId)
            : null;

        if (! $setting) {
            $setting = new Setting();
        }

        $setting->fill($validated);
        $setting->save();

        $this->settingId = $setting->id;

        $this->dispatch('toast-show', [
            'message' => 'Settings saved successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }
};
?>

<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div>
        <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Settings</h1>
        <p class="mt-1 text-sm text-slate-500">Manage contact details and social profile links for your website.</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Addresses</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="address_1" class="mb-1 block text-sm font-medium text-slate-700">Address 1</label>
                    <textarea id="address_1" wire:model.defer="address_1" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Enter primary address"></textarea>
                    @error('address_1')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address_2" class="mb-1 block text-sm font-medium text-slate-700">Address 2</label>
                    <textarea id="address_2" wire:model.defer="address_2" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Enter secondary address"></textarea>
                    @error('address_2')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Contact</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="phone_1" class="mb-1 block text-sm font-medium text-slate-700">Phone Number 1</label>
                    <input id="phone_1" type="text" wire:model.defer="phone_1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="+1 555 000 1111">
                    @error('phone_1')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_2" class="mb-1 block text-sm font-medium text-slate-700">Phone Number 2</label>
                    <input id="phone_2" type="text" wire:model.defer="phone_2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="+1 555 000 2222">
                    @error('phone_2')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email_1" class="mb-1 block text-sm font-medium text-slate-700">Email 1</label>
                    <input id="email_1" type="email" wire:model.defer="email_1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="hello@example.com">
                    @error('email_1')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email_2" class="mb-1 block text-sm font-medium text-slate-700">Email 2</label>
                    <input id="email_2" type="email" wire:model.defer="email_2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="support@example.com">
                    @error('email_2')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Social Links</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label for="instagram_url" class="mb-1 block text-sm font-medium text-slate-700">Instagram URL</label>
                    <input id="instagram_url" type="url" wire:model.defer="instagram_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="https://instagram.com/your-page">
                    @error('instagram_url')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="linkedin_url" class="mb-1 block text-sm font-medium text-slate-700">LinkedIn URL</label>
                    <input id="linkedin_url" type="url" wire:model.defer="linkedin_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="https://linkedin.com/company/your-page">
                    @error('linkedin_url')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="facebook_url" class="mb-1 block text-sm font-medium text-slate-700">Facebook URL</label>
                    <input id="facebook_url" type="url" wire:model.defer="facebook_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="https://facebook.com/your-page">
                    @error('facebook_url')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-800">
                <i class="ri-save-line text-base"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>