<?php

use App\Models\AppliedJobs;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public int $perPage = 10;
    public ?int $viewApplicationId = null;

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

    public function updateStatus(int $id, string $status): void
    {
        $allowedStatuses = ['new', 'reviewed', 'shortlisted', 'rejected', 'contacted'];

        if (! in_array($status, $allowedStatuses, true)) {
            return;
        }

        $application = AppliedJobs::query()->findOrFail($id);
        $application->status = $status;
        $application->reviewed_at = now();
        $application->save();

        $this->dispatch('toast-show', [
            'message' => 'Application status updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function openViewModal(int $id): void
    {
        $this->viewApplicationId = $id;
    }

    public function getViewApplicationProperty(): ?AppliedJobs
    {
        if (! $this->viewApplicationId) {
            return null;
        }

        return AppliedJobs::query()
            ->with('jobProfile:id,job_title')
            ->find($this->viewApplicationId);
    }

    public function getApplicationsProperty()
    {
        return AppliedJobs::query()
            ->with('jobProfile:id,job_title')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('job_title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate($this->perPage);
    }
};