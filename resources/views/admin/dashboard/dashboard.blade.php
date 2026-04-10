
<div>
    <div class="mx-auto max-w-7xl space-y-5 px-4 py-5 sm:space-y-6 sm:px-6 sm:py-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-linear-to-r from-cyan-50 via-sky-50 to-emerald-50 p-6 shadow-sm">
            <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-cyan-200/40 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-12 left-20 h-40 w-40 rounded-full bg-emerald-200/40 blur-2xl"></div>

            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">Dashboard</h1>
                    <p class="mt-1 text-sm text-slate-600">Overview of projects, jobs, contacts, and recent activity.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.projects') }}" wire:navigate class="rounded-lg border border-cyan-200 bg-white/80 px-3 py-2 text-xs font-medium text-cyan-800 transition hover:bg-white">Manage Projects</a>
                    <a href="{{ route('admin.job-applied-list') }}" wire:navigate class="rounded-lg border border-emerald-200 bg-white/80 px-3 py-2 text-xs font-medium text-emerald-800 transition hover:bg-white">View Applications</a>
                    <a href="{{ route('admin.contacts') }}" wire:navigate class="rounded-lg border border-sky-200 bg-white/80 px-3 py-2 text-xs font-medium text-sky-800 transition hover:bg-white">Open Contacts</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-cyan-200 bg-linear-to-br from-cyan-50 to-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-cyan-700">Projects</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['projects_total'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-cyan-700">Active: {{ number_format($stats['projects_active'] ?? 0) }}</p>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-linear-to-br from-blue-50 to-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-blue-700">Project Categories</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['project_categories_total'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-blue-700">Slider Items: {{ number_format($stats['slider_items_total'] ?? 0) }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-linear-to-br from-emerald-50 to-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-emerald-700">Job Profiles</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['job_profiles_total'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-emerald-700">Active: {{ number_format($stats['job_profiles_active'] ?? 0) }}</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-linear-to-br from-amber-50 to-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-amber-700">Enquiries</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format(($stats['job_applications_total'] ?? 0) + ($stats['contacts_total'] ?? 0)) }}</p>
                <p class="mt-1 text-xs text-amber-700">Applications: {{ number_format($stats['job_applications_total'] ?? 0) }} | Contacts: {{ number_format($stats['contacts_total'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <section class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Job Application Status</h2>
                    <a href="{{ route('admin.job-applied-list') }}" wire:navigate class="text-xs font-medium text-emerald-700 hover:underline">Open</a>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs sm:grid-cols-5">
                    @foreach (['new', 'reviewed', 'shortlisted', 'contacted', 'rejected'] as $status)
                        <div class="rounded-lg bg-emerald-50 px-3 py-2">
                            <p class="capitalize text-emerald-700">{{ str_replace('_', ' ', $status) }}</p>
                            <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format($jobStatusStats[$status] ?? 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-sky-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-800">Contact Status</h2>
                    <a href="{{ route('admin.contacts') }}" wire:navigate class="text-xs font-medium text-sky-700 hover:underline">Open</a>
                </div>

                <div class="grid grid-cols-3 gap-2 text-xs">
                    @foreach (['new', 'in_progress', 'closed'] as $status)
                        <div class="rounded-lg bg-sky-50 px-3 py-2">
                            <p class="capitalize text-sky-700">{{ str_replace('_', ' ', $status) }}</p>
                            <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format($contactStatusStats[$status] ?? 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Recent Applications</h2>
                    <a href="{{ route('admin.job-applied-list') }}" wire:navigate class="text-xs font-medium text-emerald-700 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Candidate</th>
                                <th class="px-4 py-3 text-left">Job</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentApplications as $application)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-900">{{ $application->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $application->email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $application->job_title ?: ($application->jobProfile?->job_title ?? 'N/A') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-md bg-emerald-100 px-2 py-1 text-xs capitalize text-emerald-800">{{ $application->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-xs text-slate-500">No applications yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Recent Contacts</h2>
                    <a href="{{ route('admin.contacts') }}" wire:navigate class="text-xs font-medium text-emerald-700 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Subject</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentContacts as $contact)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-900">{{ $contact->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $contact->email ?: 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $contact->subject ?: 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-md bg-sky-100 px-2 py-1 text-xs capitalize text-sky-800">{{ str_replace('_', ' ', $contact->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-xs text-slate-500">No contacts yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>