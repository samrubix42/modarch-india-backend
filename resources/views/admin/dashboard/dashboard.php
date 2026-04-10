<?php

use App\Models\AppliedJobs;
use App\Models\Contact;
use App\Models\JobProfile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectSlider;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    public array $stats = [];
    public array $jobStatusStats = [];
    public array $contactStatusStats = [];
    public $recentApplications;
    public $recentContacts;

    public function mount(): void
    {
        $this->loadDashboard();
    }

    public function loadDashboard(): void
    {
        $this->stats = [
            'projects_total' => Project::query()->count(),
            'projects_active' => Project::query()->where('is_active', true)->count(),
            'project_categories_total' => ProjectCategory::query()->count(),
            'slider_items_total' => ProjectSlider::query()->count(),
            'job_profiles_total' => JobProfile::query()->count(),
            'job_profiles_active' => JobProfile::query()->where('is_active', true)->count(),
            'job_applications_total' => AppliedJobs::query()->count(),
            'contacts_total' => Contact::query()->count(),
        ];

        $jobCounts = AppliedJobs::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $contactCounts = Contact::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $this->jobStatusStats = array_replace([
            'new' => 0,
            'reviewed' => 0,
            'shortlisted' => 0,
            'contacted' => 0,
            'rejected' => 0,
        ], $jobCounts);

        $this->contactStatusStats = array_replace([
            'new' => 0,
            'in_progress' => 0,
            'closed' => 0,
        ], $contactCounts);

        $this->recentApplications = AppliedJobs::query()
            ->with('jobProfile:id,job_title')
            ->latest()
            ->limit(6)
            ->get(['id', 'job_profile_id', 'job_title', 'name', 'email', 'status', 'created_at']);

        $this->recentContacts = Contact::query()
            ->latest()
            ->limit(6)
            ->get(['id', 'name', 'email', 'subject', 'status', 'created_at']);
    }
};