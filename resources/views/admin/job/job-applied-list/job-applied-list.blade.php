
<div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Job Applications</h1>
            <p class="mt-1 text-sm text-slate-500">Review submitted applications and manage candidate statuses.</p>
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
                placeholder="Search by name, email, phone, city, or job title"
                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
        </div>

        <div>
            <select wire:model.live="statusFilter" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                <option value="all">All Statuses</option>
                <option value="new">New</option>
                <option value="reviewed">Reviewed</option>
                <option value="shortlisted">Shortlisted</option>
                <option value="contacted">Contacted</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Candidate</th>
                        <th class="px-6 py-4 text-left">Job</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Submitted</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->applications as $application)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 align-top">
                                <p class="font-medium text-slate-900">{{ $application->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $application->email }}</p>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-700">
                                {{ $application->job_title ?: ($application->jobProfile?->job_title ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <select
                                    wire:change="updateStatus({{ $application->id }}, $event.target.value)"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium capitalize text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                >
                                    @foreach (['new', 'reviewed', 'shortlisted', 'contacted', 'rejected'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 align-top text-xs text-slate-500">
                                <p>{{ $application->created_at?->format('d M Y') }}</p>
                                @if ($application->reviewed_at)
                                    <p class="mt-1">Reviewed {{ $application->reviewed_at->format('d M') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="$dispatch('open-view-modal'); $wire.openViewModal({{ $application->id }})" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-200">View</button>

                                    @if ($application->resume_path)
                                        <a href="{{ route('admin.applied-jobs.download', ['appliedJob' => $application->id, 'type' => 'resume']) }}" class="rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">Resume</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-3 lg:hidden">
            @forelse ($this->applications as $application)
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium text-slate-900">{{ $application->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $application->email }}</p>
                        </div>
                        <p class="text-[11px] text-slate-500">{{ $application->created_at?->format('d M Y') }}</p>
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-2 text-xs">
                        <p><span class="text-slate-400">Job:</span> <span class="text-slate-700">{{ $application->job_title ?: ($application->jobProfile?->job_title ?? 'N/A') }}</span></p>
                        <p><span class="text-slate-400">Email:</span> <span class="text-slate-700">{{ $application->email }}</span></p>
                        <p><span class="text-slate-400">Phone:</span> <span class="text-slate-700">{{ $application->phone }}</span></p>
                        <p><span class="text-slate-400">City:</span> <span class="text-slate-700">{{ $application->city ?: 'N/A' }}</span></p>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <select
                            wire:change="updateStatus({{ $application->id }}, $event.target.value)"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium capitalize text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        >
                            @foreach (['new', 'reviewed', 'shortlisted', 'contacted', 'rejected'] as $status)
                                <option value="{{ $status }}" @selected($application->status === $status)>{{ $status }}</option>
                            @endforeach
                        </select>

                        <button type="button" @click="$dispatch('open-view-modal'); $wire.openViewModal({{ $application->id }})" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-700">View</button>

                        @if ($application->resume_path)
                            <a href="{{ route('admin.applied-jobs.download', ['appliedJob' => $application->id, 'type' => 'resume']) }}" class="col-span-2 rounded-lg bg-emerald-50 px-3 py-2 text-center text-xs font-medium text-emerald-700">Download Resume</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-400">
                    No applications found.
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
                {{ $this->applications->links() }}
            </div>
        </div>
    </div>

    <div x-data="{ viewOpen: false }" x-on:open-view-modal.window="viewOpen = true" x-on:close-view-modal.window="viewOpen = false" x-cloak>
        <template x-teleport="body">
            <div x-show="viewOpen" class="fixed inset-0 z-95 flex items-center justify-center p-4">
                <div @click="viewOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

                <div x-show="viewOpen" x-transition x-trap.inert.noscroll="viewOpen" class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                    @php($selected = $this->viewApplication)

                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Application Details</h3>
                        <button @click="viewOpen=false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <i class="ri-close-line text-lg"></i>
                        </button>
                    </div>

                    @if ($selected)
                        <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                            <p><span class="text-slate-400">Name:</span> <span class="text-slate-800">{{ $selected->name }}</span></p>
                            <p><span class="text-slate-400">Email:</span> <span class="text-slate-800">{{ $selected->email }}</span></p>
                            <p><span class="text-slate-400">Phone:</span> <span class="text-slate-800">{{ $selected->phone }}</span></p>
                            <p><span class="text-slate-400">City:</span> <span class="text-slate-800">{{ $selected->city ?: 'N/A' }}</span></p>
                            <p class="sm:col-span-2"><span class="text-slate-400">Job:</span> <span class="text-slate-800">{{ $selected->job_title ?: ($selected->jobProfile?->job_title ?? 'N/A') }}</span></p>
                            <p><span class="text-slate-400">Status:</span> <span class="capitalize text-slate-800">{{ $selected->status }}</span></p>
                            <p><span class="text-slate-400">Submitted:</span> <span class="text-slate-800">{{ $selected->created_at?->format('d M Y, h:i A') }}</span></p>

                            @if ($selected->portfolio_url)
                                <p class="sm:col-span-2">
                                    <span class="text-slate-400">Portfolio URL:</span>
                                    <a href="{{ $selected->portfolio_url }}" target="_blank" class="text-emerald-700 underline">Open link</a>
                                </p>
                            @endif

                            @if ($selected->message)
                                <div class="sm:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Message</p>
                                    <p class="mt-1 text-sm text-slate-700">{{ $selected->message }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-200 pt-4">
                            @if ($selected->resume_path)
                                <a href="{{ route('admin.applied-jobs.download', ['appliedJob' => $selected->id, 'type' => 'resume']) }}" class="rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">Download Resume</a>
                            @endif

                            @if ($selected->portfolio_path)
                                <a href="{{ route('admin.applied-jobs.download', ['appliedJob' => $selected->id, 'type' => 'portfolio']) }}" class="rounded-md bg-sky-50 px-3 py-1.5 text-xs font-medium text-sky-700">Download Portfolio</a>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No application selected.</p>
                    @endif
                </div>
            </div>
        </template>
    </div>
</div>