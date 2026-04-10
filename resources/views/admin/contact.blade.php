<?php

use App\Models\Contact;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public int $perPage = 10;
    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        Contact::query()->whereKey($targetId)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Contact deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->deleteId = null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $allowedStatuses = ['new', 'in_progress', 'closed'];

        if (! in_array($status, $allowedStatuses, true)) {
            return;
        }

        $contact = Contact::query()->findOrFail($id);
        $contact->status = $status;
        $contact->save();

        $this->dispatch('toast-show', [
            'message' => 'Contact status updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function getContactsProperty()
    {
        return Contact::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('message', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);
    }
};
?>

<div>
    <div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Contact Management</h1>
                <p class="mt-1 text-sm text-slate-500">Manage contact enquiries and track their progress.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="relative sm:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="ri-search-line"></i>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, email, phone, subject, or message"
                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
            </div>

            <div>
                <select wire:model.live="statusFilter" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    <option value="all">All Statuses</option>
                    <option value="new">New</option>
                    <option value="in_progress">In Progress</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4 text-left">Contact</th>
                            <th class="px-6 py-4 text-left">Subject</th>
                            <th class="px-6 py-4 text-left">Message</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Received</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->contacts as $contact)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 align-top">
                                    <p class="font-medium text-slate-900">{{ $contact->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $contact->email ?: 'N/A' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $contact->phone ?: 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 align-top text-slate-700">
                                    {{ $contact->subject ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 align-top text-slate-600">
                                    <p class="line-clamp-3 max-w-xs text-xs">{{ $contact->message ?: 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <select
                                        wire:change="updateStatus({{ $contact->id }}, $event.target.value)"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium capitalize text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                    >
                                        @foreach (['new', 'in_progress', 'closed'] as $status)
                                            <option value="{{ $status }}" @selected($contact->status === $status)>{{ str_replace('_', ' ', $status) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 align-top text-xs text-slate-500">
                                    {{ $contact->created_at?->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $contact->id }})" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-100">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">No contacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 p-3 lg:hidden">
                @forelse ($this->contacts as $contact)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $contact->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $contact->email ?: 'N/A' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $contact->phone ?: 'N/A' }}</p>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $contact->created_at?->format('d M Y') }}</p>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2 text-xs">
                            <p><span class="text-slate-400">Subject:</span> <span class="text-slate-700">{{ $contact->subject ?: 'N/A' }}</span></p>
                            <p><span class="text-slate-400">Message:</span> <span class="text-slate-700">{{ $contact->message ?: 'N/A' }}</span></p>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <select
                                wire:change="updateStatus({{ $contact->id }}, $event.target.value)"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium capitalize text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            >
                                @foreach (['new', 'in_progress', 'closed'] as $status)
                                    <option value="{{ $status }}" @selected($contact->status === $status)>{{ str_replace('_', ' ', $status) }}</option>
                                @endforeach
                            </select>
                            <button type="button" @click="$dispatch('open-delete-modal'); $wire.confirmDelete({{ $contact->id }})" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700">Delete</button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                        No contacts found.
                    </div>
                @endforelse
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span>Per page</span>
                    <select wire:model.live="perPage" class="rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-700 outline-none focus:border-emerald-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div>
                    {{ $this->contacts->links() }}
                </div>
            </div>
        </div>

        <div x-data="{ deleteOpen: false }" x-on:open-delete-modal.window="deleteOpen = true" x-on:close-delete-modal.window="deleteOpen = false" x-cloak>
            <template x-teleport="body">
                <div x-show="deleteOpen" class="fixed inset-0 z-95 flex items-center justify-center px-4">
                    <div @click="deleteOpen = false" class="absolute inset-0 bg-slate-900/50"></div>

                    <div x-show="deleteOpen" x-transition x-trap.inert.noscroll="deleteOpen" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <h3 class="text-lg font-semibold text-slate-900">Delete Contact</h3>
                        <p class="mt-2 text-sm text-slate-600">This action cannot be undone. Are you sure you want to delete this contact?</p>

                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="deleteOpen = false" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                            <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>