<?php

use App\Models\ProjectStatus;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public string $search = '';
    public ?int $statusId = null;
    public ?int $deleteId = null;
    public $statuses = [];

    public string $name = '';
    public string $slug = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
        $this->loadStatuses();
    }

    public function updatedSearch(): void
    {
        $this->loadStatuses();
    }

    public function loadStatuses(): void
    {
        $this->statuses = ProjectStatus::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function updatedName(string $value): void
    {
        if ($this->slug === '' || $this->statusId === null) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->statusId = null;
        $this->name = '';
        $this->slug = '';
        $this->sort_order = (int) (ProjectStatus::max('sort_order') ?? 0) + 1;
        $this->is_active = true;
        $this->deleteId = null;
    }

    public function openEditModal(int $id): void
    {
        $status = ProjectStatus::findOrFail($id);

        $this->resetValidation();
        $this->statusId = $status->id;
        $this->name = $status->name;
        $this->slug = $status->slug;
        $this->sort_order = $status->sort_order;
        $this->is_active = (bool) $status->is_active;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('project_statuses', 'slug')->ignore($this->statusId)],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $model = $this->statusId
            ? ProjectStatus::findOrFail($this->statusId)
            : new ProjectStatus();

        $model->fill([
            'name' => $validated['name'],
            'slug' => str($validated['slug'])->slug()->toString(),
            'sort_order' => $validated['sort_order'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        $model->save();

        $this->dispatch('toast-show', [
            'message' => $this->statusId ? 'Status updated successfully!' : 'Status created successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-modal');
        $this->loadStatuses();
        $this->resetForm();
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        ProjectStatus::whereKey($targetId)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Status deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->loadStatuses();
        $this->deleteId = null;
    }

    public function toggleStatus(int $id): void
    {
        $status = ProjectStatus::findOrFail($id);
        $status->is_active = ! (bool) $status->is_active;
        $status->save();

        if ($this->statuses instanceof \Illuminate\Support\Collection) {
            $this->statuses = $this->statuses->map(function ($item) use ($id, $status) {
                if ((int) $item->id === $id) {
                    $item->is_active = (bool) $status->is_active;
                }

                return $item;
            });
        }

        $this->dispatch('toast-show', [
            'message' => 'Status updated successfully!',
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

        $orderedIds = ProjectStatus::query()
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
            ProjectStatus::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Status order updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->loadStatuses();
    }
};