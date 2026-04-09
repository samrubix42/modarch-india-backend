<?php

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ProjectTag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public int $page = 1;

    public ?int $projectId = null;
    public ?int $deleteId = null;

    public ?int $tag_id = null;
    public ?int $project_status_id = null;
    public string $client_name = '';
    public string $project_name = '';
    public ?string $project_address = null;
    public string $site_area = '';
    public ?string $built_up_area = null;
    public string $slug = '';
    public bool $is_active = true;

    public $project_thumbnail = null;
    public $project_main_image = null;
    public ?string $existing_thumbnail = null;
    public ?string $existing_main_image = null;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedPerPage($value): void
    {
        $allowed = [10, 25, 50];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->page = 1;
    }

    public function updatedProjectName(string $value): void
    {
        if ($this->slug === '' || $this->projectId === null) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->projectId = null;
        $this->tag_id = null;
        $this->project_status_id = null;
        $this->client_name = '';
        $this->project_name = '';
        $this->project_address = null;
        $this->site_area = '';
        $this->built_up_area = null;
        $this->slug = '';
        $this->is_active = true;
        $this->project_thumbnail = null;
        $this->project_main_image = null;
        $this->existing_thumbnail = null;
        $this->existing_main_image = null;
        $this->deleteId = null;
    }

    public function openEditModal(int $id): void
    {
        $project = Project::findOrFail($id);

        $this->resetValidation();
        $this->projectId = $project->id;
        $this->tag_id = $project->tag_id;
        $this->project_status_id = $project->project_status_id;
        $this->client_name = $project->client_name;
        $this->project_name = $project->project_name;
        $this->project_address = $project->project_address;
        $this->site_area = $project->site_area;
        $this->built_up_area = $project->built_up_area;
        $this->slug = $project->slug;
        $this->is_active = (bool) $project->is_active;

        $this->project_thumbnail = null;
        $this->project_main_image = null;
        $this->existing_thumbnail = $project->project_thumbnail;
        $this->existing_main_image = $project->project_main_image;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'tag_id' => ['nullable', 'exists:project_tags,id'],
            'project_status_id' => ['nullable', 'exists:project_statuses,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'project_name' => ['required', 'string', 'max:255'],
            'project_address' => ['nullable', 'string', 'max:255'],
            'site_area' => ['required', 'string', 'max:255'],
            'built_up_area' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($this->projectId)],
            'project_thumbnail' => ['nullable', 'image', 'max:4096'],
            'project_main_image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['required', 'boolean'],
        ]);

        $project = $this->projectId ? Project::findOrFail($this->projectId) : new Project();

        $thumbnailPath = $project->project_thumbnail;
        if ($this->project_thumbnail) {
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $this->project_thumbnail->store('projects/thumbnails', 'public');
        }

        $mainImagePath = $project->project_main_image;
        if ($this->project_main_image) {
            if ($mainImagePath && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            $mainImagePath = $this->project_main_image->store('projects/main-images', 'public');
        }

        $project->fill([
            'tag_id' => $validated['tag_id'],
            'project_status_id' => $validated['project_status_id'],
            'client_name' => $validated['client_name'],
            'project_name' => $validated['project_name'],
            'project_address' => $validated['project_address'],
            'site_area' => $validated['site_area'],
            'built_up_area' => $validated['built_up_area'],
            'slug' => str($validated['slug'])->slug()->toString(),
            'project_thumbnail' => $thumbnailPath,
            'project_main_image' => $mainImagePath,
            'is_active' => (bool) $validated['is_active'],
        ]);

        $project->save();

        $this->dispatch('toast-show', [
            'message' => $this->projectId ? 'Project updated successfully!' : 'Project created successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        $project = Project::findOrFail($targetId);

        if ($project->project_thumbnail && Storage::disk('public')->exists($project->project_thumbnail)) {
            Storage::disk('public')->delete($project->project_thumbnail);
        }

        if ($project->project_main_image && Storage::disk('public')->exists($project->project_main_image)) {
            Storage::disk('public')->delete($project->project_main_image);
        }

        $project->delete();

        $this->dispatch('toast-show', [
            'message' => 'Project deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->deleteId = null;
    }

    public function toggleStatus(int $id): void
    {
        $project = Project::findOrFail($id);
        $project->is_active = ! (bool) $project->is_active;
        $project->save();

        $this->dispatch('toast-show', [
            'message' => 'Project status updated successfully!',
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

        $orderedIds = Project::query()->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $currentIndex = array_search($itemId, $orderedIds, true);
        if ($currentIndex === false) {
            return;
        }

        array_splice($orderedIds, $currentIndex, 1);
        if ($targetPosition > count($orderedIds)) {
            $targetPosition = count($orderedIds);
        }
        array_splice($orderedIds, $targetPosition, 0, [$itemId]);

        // Keep a stable visual order by syncing sorted ids to created_at sequence.
        foreach ($orderedIds as $index => $id) {
            Project::whereKey($id)->update(['updated_at' => now()->subSeconds(count($orderedIds) - $index)]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Project order updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage(): void
    {
        if ($this->page < $this->projectsPaginator()->lastPage()) {
            $this->page++;
        }
    }

    public function gotoPage(int $page): void
    {
        $last = $this->projectsPaginator()->lastPage();
        $this->page = min(max(1, $page), max(1, $last));
    }

    public function projectsPaginator(): LengthAwarePaginator
    {
        return Project::query()
            ->with(['tag', 'status'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('project_name', 'like', '%' . $this->search . '%')
                        ->orWhere('client_name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    public function tagOptions()
    {
        return ProjectTag::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }

    public function statusOptions()
    {
        return ProjectStatus::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }
};