<?php

use App\Enums\ContentType;
use App\Models\Project;
use App\Models\ProjectSlider;
use Illuminate\Pagination\LengthAwarePaginator;
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
    public int $page = 1;

    public ?int $sliderId = null;
    public ?int $deleteId = null;
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
        $this->page = 1;
    }

    public function updatedPerPage($value): void
    {
        $allowed = [10, 25, 50];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->page = 1;
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

        $this->dispatch('tinymce-set-project-slider-description', content: $this->description ?? '');
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->sliderId = null;
        $this->type = ContentType::IMAGE->value;
        $this->image_files = [];
        $this->video_file = null;
        $this->description = null;
        $this->width = '100';
        $this->sort_order = $this->nextSortOrder();
        $this->existing_image = null;
        $this->existing_video = null;
        $this->deleteId = null;

        $this->dispatch('tinymce-set-project-slider-description', content: '');
    }

    public function openEditModal(int $id): void
    {
        $slider = ProjectSlider::where('project_id', $this->projectId)->findOrFail($id);

        $this->resetValidation();
        $this->sliderId = $slider->id;
        $this->type = $slider->type ?? ContentType::IMAGE->value;
        $this->description = $slider->description;
        $this->width = $slider->width ?: '100';
        $this->sort_order = $slider->sort_order;
        $this->existing_image = $slider->image;
        $this->existing_video = $slider->video;
        $this->image_files = [];
        $this->video_file = null;

        $this->dispatch('tinymce-set-project-slider-description', content: $this->description ?? '');
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
            'type' => ['required', 'in:' . implode(',', ContentType::values())],
            'image_files' => ['nullable', 'array'],
            'image_files.*' => ['image', 'max:6144'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
            'description' => ['nullable', 'string'],
            'width' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:1'],
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
                $this->nextSortOrder()
            );

            foreach (array_values($this->image_files) as $index => $imageFile) {
                $imagePath = $imageFile->store('projects/sliders/images', 'public');

                ProjectSlider::create([
                    'project_id' => $this->projectId,
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
            'type' => $validated['type'],
            'image' => $validated['type'] === ContentType::IMAGE->value ? $imagePath : null,
            'video' => $validated['type'] === ContentType::VIDEO->value ? $videoPath : null,
            'description' => $validated['type'] === ContentType::DESCRIPTION->value ? $validated['description'] : null,
            'width' => $validated['width'] ?: '100',
            'sort_order' => $this->sliderId
                ? (int) $validated['sort_order']
                : max((int) $validated['sort_order'], $this->nextSortOrder()),
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
            'message' => 'Slider order updated successfully!',
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
        if ($this->page < $this->slidersPaginator()->lastPage()) {
            $this->page++;
        }
    }

    public function gotoPage(int $page): void
    {
        $last = $this->slidersPaginator()->lastPage();
        $this->page = min(max(1, $page), max(1, $last));
    }

    public function slidersPaginator(): LengthAwarePaginator
    {
        return ProjectSlider::query()
            ->where('project_id', $this->projectId)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('type', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    protected function nextSortOrder(): int
    {
        $maxSort = (int) (
            ProjectSlider::query()
                ->where('project_id', $this->projectId)
                ->max('sort_order') ?? 0
        );

        return $maxSort + 1;
    }

    public function typeOptions(): array
    {
        return ContentType::cases();
    }
};