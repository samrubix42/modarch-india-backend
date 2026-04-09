<?php

use App\Models\JobProfile;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public string $search = '';
    public ?int $profileId = null;
    public ?int $deleteId = null;
    public $profiles = [];

    public string $job_title = '';
    public string $job_description = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
        $this->loadProfiles();
    }

    public function updatedSearch(): void
    {
        $this->loadProfiles();
    }

    public function loadProfiles(): void
    {
        $this->profiles = JobProfile::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('job_title', 'like', '%' . $this->search . '%')
                        ->orWhere('job_description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('job_title')
            ->get();
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->profileId = null;
        $this->job_title = '';
        $this->job_description = '';
        $this->sort_order = (int) (JobProfile::max('sort_order') ?? 0) + 1;
        $this->is_active = true;
        $this->deleteId = null;

        $this->dispatch('tinymce-set-content', content: '');
    }

    public function openEditModal(int $id): void
    {
        $profile = JobProfile::findOrFail($id);

        $this->resetValidation();
        $this->profileId = $profile->id;
        $this->job_title = $profile->job_title;
        $this->job_description = (string) $profile->job_description;
        $this->sort_order = $profile->sort_order;
        $this->is_active = (bool) $profile->is_active;

        $this->dispatch('tinymce-set-content', content: $this->job_description);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'job_title' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $model = $this->profileId
            ? JobProfile::findOrFail($this->profileId)
            : new JobProfile();

        $model->fill([
            'job_title' => $validated['job_title'],
            'job_description' => $validated['job_description'],
            'sort_order' => $validated['sort_order'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        $model->save();

        $this->dispatch('toast-show', [
            'message' => $this->profileId ? 'Job profile updated successfully!' : 'Job profile created successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-modal');
        $this->loadProfiles();
        $this->resetForm();
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        JobProfile::whereKey($targetId)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Job profile deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->loadProfiles();
        $this->deleteId = null;
    }

    public function toggleStatus(int $id): void
    {
        $profile = JobProfile::findOrFail($id);
        $profile->is_active = ! (bool) $profile->is_active;
        $profile->save();

        if ($this->profiles instanceof \Illuminate\Support\Collection) {
            $this->profiles = $this->profiles->map(function ($item) use ($id, $profile) {
                if ((int) $item->id === $id) {
                    $item->is_active = (bool) $profile->is_active;
                }

                return $item;
            });
        }

        $this->dispatch('toast-show', [
            'message' => 'Job profile status updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function sortItem(...$payload): void
    {
        $item = $payload[0] ?? null;
        $position = $payload[1] ?? 0;

        if (is_array($item) && isset($item['value'])) {
            $itemId = (int) $item['value'];
        } elseif (is_scalar($item)) {
            $itemId = (int) $item;
        } else {
            $itemId = 0;
        }

        $targetPosition = max(0, (int) $position);

        if ($itemId === 0) {
            return;
        }

        $orderedIds = JobProfile::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $currentIndex = array_search($itemId, $orderedIds, true);

        if ($currentIndex === false) {
            return;
        }

        array_splice($orderedIds, $currentIndex, 1);

        if ($targetPosition > count($orderedIds)) {
            $targetPosition = count($orderedIds);
        }

        array_splice($orderedIds, $targetPosition, 0, [$itemId]);

        foreach ($orderedIds as $index => $id) {
            JobProfile::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Job profile order updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->loadProfiles();
    }
};