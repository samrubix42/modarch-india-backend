<?php

use App\Models\ProjectCategory;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public string $search = '';
    public ?int $categoryId = null;
    public ?int $deleteId = null;
    public $categories = [];

    public string $name = '';
    public string $slug = '';
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public int $sort_order = 0;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
        $this->loadCategories();
    }

    public function updatedSearch(): void
    {
        $this->loadCategories();
    }

   
    public function loadCategories(): void
    {
        $this->categories = ProjectCategory::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%')
                        ->orWhere('meta_title', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function updatedName(string $value): void
    {
        if ($this->slug === '' || $this->categoryId === null) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->meta_title = null;
        $this->meta_description = null;
        $this->sort_order = (int) (ProjectCategory::max('sort_order') ?? 0) + 1;
        $this->is_active = true;
        $this->deleteId = null;
    }

    public function openEditModal(int $id): void
    {
        $category = ProjectCategory::findOrFail($id);

        $this->resetValidation();
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->meta_title = $category->meta_title;
        $this->meta_description = $category->meta_description;
        $this->sort_order = $category->sort_order;
        $this->is_active = (bool) $category->is_active;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('project_categories', 'slug')->ignore($this->categoryId)],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $model = $this->categoryId
            ? ProjectCategory::findOrFail($this->categoryId)
            : new ProjectCategory();

        $model->fill([
            'name' => $validated['name'],
            'slug' => str($validated['slug'])->slug()->toString(),
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'sort_order' => $validated['sort_order'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        $model->save();

        $this->dispatch('toast-show', [
            'message' => $this->categoryId ? 'Category updated successfully!' : 'Category created successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-modal');
        $this->loadCategories();
        $this->resetForm();
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (! $targetId) {
            return;
        }

        ProjectCategory::whereKey($targetId)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Category deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->loadCategories();
        $this->deleteId = null;
    }

    public function toggleStatus(int $id): void
    {
        $category = ProjectCategory::findOrFail($id);
        $category->is_active = ! (bool) $category->is_active;
        $category->save();

        if ($this->categories instanceof \Illuminate\Support\Collection) {
            $this->categories = $this->categories->map(function ($item) use ($id, $category) {
                if ((int) $item->id === $id) {
                    $item->is_active = (bool) $category->is_active;
                }

                return $item;
            });
        }

        $this->dispatch('toast-show', [
            'message' => 'Category status updated successfully!',
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

        $orderedIds = ProjectCategory::query()
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
            ProjectCategory::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Category order updated successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->loadCategories();
    }
};
?>
