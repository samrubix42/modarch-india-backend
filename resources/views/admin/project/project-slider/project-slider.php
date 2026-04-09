<?php

use App\Enums\ContentType;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectSlider;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    public Project $project;
    public int $projectId;

    public string $search = '';
    public int $perPage = 10;
    public array $categoryPages = [];

    public ?int $sliderId = null;
    public ?int $deleteId = null;

    public ?int $project_category_id = null;
    public string $type = 'image';
    public array $image_files = [];
    public $video_file = null;
    public ?string $description = null;
    public string $width = '100';
    public int $sort_order = 0;

    public ?string $existing_image = null;
    public ?string $existing_video = null;

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->projectId = $project->id;
        $this->resetForm();
    }

    public function updatedSearch(): void
    {
        $this->categoryPages = [];
    }

    public function updatedPerPage($value): void
    {
        $allowed = [10, 25, 50];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->categoryPages = [];
    }

    public function updatedType(): void
    {
        if ($this->type !== ContentType::IMAGE->value) {
            $this->image_files = [];
        }

        if ($this->type !== ContentType::VIDEO->value) {
            $this->video_file = null;
        }

        if ($this->type !== ContentType::DESCRIPTION->value) {
            $this->description = null;
        }
    }

    public function updatedProjectCategoryId($value): void
    {
        if ($this->sliderId !== null || blank($value)) {
            return;
        }

        $this->sort_order = $this->nextSortOrderForCategory((int) $value);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->sliderId = null;
        $this->project_category_id = null;
        $this->type = ContentType::IMAGE->value;
        $this->image_files = [];
        $this->video_file = null;
        $this->description = null;
        $this->width = '100';
        $this->sort_order = (int) (ProjectSlider::where('project_id', $this->projectId)->max('sort_order') ?? 0) + 1;
        $this->existing_image = null;
        $this->existing_video = null;
        $this->deleteId = null;
    }

    public function openEditModal(int $id): void
    {
        $slider = ProjectSlider::where('project_id', $this->projectId)->findOrFail($id);

        $this->resetValidation();
        $this->sliderId = $slider->id;
        $this->project_category_id = $slider->project_category_id;
        $this->type = $slider->type ?? ContentType::IMAGE->value;
        $this->description = $slider->description;
        $this->width = $slider->width ?: '100';
        $this->sort_order = $slider->sort_order;
        $this->existing_image = $slider->image;
        $this->existing_video = $slider->video;
        $this->image_files = [];
        $this->video_file = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function removeImageFile(int $index): void
    {
        if (! isset($this->image_files[$index])) {
            return;
        }

        unset($this->image_files[$index]);
        $this->image_files = array_values($this->image_files);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'project_category_id' => ['required', 'exists:project_categories,id'],
            'type' => ['required', 'in:' . implode(',', ContentType::values())],
            'image_files' => ['nullable', 'array'],
            'image_files.*' => ['image', 'max:6144'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
            'description' => ['nullable', 'string'],
            'width' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        if (
            $validated['type'] === ContentType::IMAGE->value
            && empty($this->image_files)
            && ! $this->existing_image
        ) {
            $this->addError('image_files', 'At least one image is required for image type.');
            return;
        }

        if ($validated['type'] === ContentType::VIDEO->value && ! $this->video_file && ! $this->existing_video) {
            $this->addError('video_file', 'Video is required for video type.');
            return;
        }

        if ($validated['type'] === ContentType::DESCRIPTION->value && blank($validated['description'])) {
            $this->addError('description', 'Description is required for description type.');
            return;
        }

        if ($validated['type'] === ContentType::IMAGE->value && ! $this->sliderId && count($this->image_files) > 1) {
            $baseSortOrder = max(
                (int) $validated['sort_order'],
                $this->nextSortOrderForCategory((int) $validated['project_category_id'])
            );

            foreach (array_values($this->image_files) as $index => $imageFile) {
                $imagePath = $imageFile->store('projects/sliders/images', 'public');

                ProjectSlider::create([
                    'project_id' => $this->projectId,
                    'project_category_id' => $validated['project_category_id'],
                    'type' => ContentType::IMAGE->value,
                    'image' => $imagePath,
                    'video' => null,
                    'description' => null,
                    'width' => $validated['width'] ?: '100',
                    'sort_order' => $baseSortOrder + $index,
                ]);
            }

            $this->dispatch('toast-show', [
                'message' => 'Slider images added successfully in upload order!',
                'type' => 'success',
                'position' => 'top-right',
            ]);

            $this->dispatch('close-modal');
            $this->resetForm();

            return;
        }

        $slider = $this->sliderId
            ? ProjectSlider::where('project_id', $this->projectId)->findOrFail($this->sliderId)
            : new ProjectSlider(['project_id' => $this->projectId]);

        $imagePath = $slider->image;
        if (! empty($this->image_files)) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image_files[0]->store('projects/sliders/images', 'public');
        }

        $videoPath = $slider->video;
        if ($this->video_file) {
            if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
            $videoPath = $this->video_file->store('projects/sliders/videos', 'public');
        }

        $slider->fill([
            'project_id' => $this->projectId,
            'project_category_id' => $validated['project_category_id'],
            'type' => $validated['type'],
            'image' => $validated['type'] === ContentType::IMAGE->value ? $imagePath : null,
            'video' => $validated['type'] === ContentType::VIDEO->value ? $videoPath : null,
            'description' => $validated['type'] === ContentType::DESCRIPTION->value ? $validated['description'] : null,
            'width' => $validated['width'] ?: '100',
            'sort_order' => $this->sliderId
                ? (int) $validated['sort_order']
                : max((int) $validated['sort_order'], $this->nextSortOrderForCategory((int) $validated['project_category_id'])),
        ]);

        $slider->save();

        $this->dispatch('toast-show', [
            'message' => $this->sliderId ? 'Slider item updated successfully!' : 'Slider item created successfully!',
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

        $slider = ProjectSlider::where('project_id', $this->projectId)->findOrFail($targetId);

        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        if ($slider->video && Storage::disk('public')->exists($slider->video)) {
            Storage::disk('public')->delete($slider->video);
        }

        $slider->delete();

        $this->dispatch('toast-show', [
            'message' => 'Slider item deleted successfully!',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->dispatch('close-delete-modal');
        $this->deleteId = null;
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

        $item = ProjectSlider::where('project_id', $this->projectId)->find($itemId);
        if (! $item) {
            return;
        }

        $orderedIds = ProjectSlider::query()
            ->where('project_id', $this->projectId)
            ->where('project_category_id', $item->project_category_id)
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
            ProjectSlider::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        $this->dispatch('toast-show', [
            'message' => 'Slider order updated for this category!',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function previousCategoryPage(int $categoryId): void
    {
        $key = (string) $categoryId;
        $current = (int) ($this->categoryPages[$key] ?? 1);
        $this->categoryPages[$key] = max(1, $current - 1);
    }

    public function nextCategoryPage(int $categoryId): void
    {
        $key = (string) $categoryId;
        $current = (int) ($this->categoryPages[$key] ?? 1);
        $last = $this->categoryLastPage($categoryId);
        $this->categoryPages[$key] = min($last, $current + 1);
    }

    public function gotoCategoryPage(int $categoryId, int $page): void
    {
        $key = (string) $categoryId;
        $last = $this->categoryLastPage($categoryId);
        $this->categoryPages[$key] = min(max(1, $page), $last);
    }

    public function sliderGroups(): array
    {
        $grouped = ProjectSlider::query()
            ->with('category')
            ->where('project_id', $this->projectId)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('type', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('project_category_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($item) => (int) ($item->project_category_id ?? 0));

        $groups = [];

        foreach ($grouped as $categoryId => $items) {
            $key = (string) $categoryId;
            $total = $items->count();
            $lastPage = max(1, (int) ceil($total / $this->perPage));
            $currentPage = min(max(1, (int) ($this->categoryPages[$key] ?? 1)), $lastPage);

            if (($this->categoryPages[$key] ?? null) !== $currentPage) {
                $this->categoryPages[$key] = $currentPage;
            }

            $offset = ($currentPage - 1) * $this->perPage;

            $groups[] = [
                'category_id' => (int) $categoryId,
                'title' => $items->first()?->category?->name ?? 'Uncategorized',
                'items' => $items->slice($offset, $this->perPage)->values(),
                'total' => $total,
                'currentPage' => $currentPage,
                'lastPage' => $lastPage,
            ];
        }

        return $groups;
    }

    protected function categoryLastPage(int $categoryId): int
    {
        $count = ProjectSlider::query()
            ->where('project_id', $this->projectId)
            ->where('project_category_id', $categoryId === 0 ? null : $categoryId)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('type', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->count();

        return max(1, (int) ceil($count / $this->perPage));
    }

    protected function nextSortOrderForCategory(int $categoryId): int
    {
        $maxSort = (int) (
            ProjectSlider::query()
                ->where('project_id', $this->projectId)
                ->where('project_category_id', $categoryId)
                ->max('sort_order') ?? 0
        );

        return $maxSort + 1;
    }

    public function categoryOptions()
    {
        return ProjectCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }

    public function typeOptions(): array
    {
        return ContentType::cases();
    }
};