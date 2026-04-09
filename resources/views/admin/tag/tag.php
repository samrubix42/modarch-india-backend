<?php

use App\Models\ProjectTag;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public string $search = '';
    public ?int $tagId = null;
    public ?int $deleteId = null;
    public $tags = [];

    public string $name = '';
    public string $slug = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
        $this->loadTags();
    }

    public function updatedSearch(): void
    {
        $this->loadTags();
    }

    public function loadTags(): void
    {
        $this->tags = ProjectTag::query()
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
        if ($this->slug === '' || $this->tagId === null) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->tagId = null;
        $this->name = '';
        $this->slug = '';
        $this->sort_order = (int) (ProjectTag::max('sort_order') ?? 0) + 1;
        $this->is_active = true;
        $this->deleteId = null;
    }

    public function openEditModal(int $id): void
    {
        $tag = ProjectTag::findOrFail($id);

        $this->resetValidation();
        $this->tagId = $tag->id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->sort_order = $tag->sort_order;
        $this->is_active = (bool) $tag->is_active;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('project_tags', 'slug')->ignore($this->tagId)],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $model = $this->tagId
            ? ProjectTag::findOrFail($this->tagId)
            : new ProjectTag();

        $model->fill([
            'name' => $validated['name'],
            'slug' => str($validated['slug'])->slug()->toString(),
            'sort_order' => $validated['sort_order'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        $model->save();

        $this->dispatch('toast-show', [
            'message' => $this->tagId ? 'Tag updated successfully!' : 'Tag created successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-modal');
        $this->loadTags();
        $this->resetForm();
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        ProjectTag::whereKey($targetId)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Tag deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->loadTags();
        $this->deleteId = null;
    }

    public function toggleStatus(int $id): void
    {
        $tag = ProjectTag::findOrFail($id);
        $tag->is_active = ! (bool) $tag->is_active;
        $tag->save();

        if ($this->tags instanceof \Illuminate\Support\Collection) {
            $this->tags = $this->tags->map(function ($item) use ($id, $tag) {
                if ((int) $item->id === $id) {
                    $item->is_active = (bool) $tag->is_active;
                }

                return $item;
            });
        }

        $this->dispatch('toast-show', [
            'message' => 'Tag status updated successfully!',
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

        $orderedIds = ProjectTag::query()
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
            ProjectTag::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Tag order updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->loadTags();
    }
};