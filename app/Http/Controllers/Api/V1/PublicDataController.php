<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicDataController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = ProjectCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'meta_title',
                'meta_description',
                'sort_order',
            ]);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function settings(): JsonResponse
    {
        $setting = Setting::query()->first([
            'address_1',
            'address_2',
            'phone_1',
            'phone_2',
            'email_1',
            'email_2',
            'instagram_url',
            'linkedin_url',
            'facebook_url',
        ]);

        return response()->json([
            'success' => true,
            'data' => $setting,
        ]);
    }

    public function projects(Request $request): JsonResponse
    {
        $projects = Project::query()
            ->with([
                'categories' => fn ($query) => $query->orderBy('sort_order')->orderBy('name'),
                'tag',
                'status',
                'sliders' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            ])
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $categoryId = (int) $request->integer('category_id');
                $query->whereHas('categories', fn ($nested) => $nested->where('project_categories.id', $categoryId));
            })
            ->when($request->filled('category_slug'), function ($query) use ($request) {
                $slug = (string) $request->string('category_slug');
                $query->whereHas('categories', fn ($nested) => $nested->where('project_categories.slug', $slug));
            })
            ->when($request->filled('tag_slug'), function ($query) use ($request) {
                $slug = (string) $request->string('tag_slug');
                $query->whereHas('tag', fn ($nested) => $nested->where('slug', $slug));
            })
            ->when($request->filled('project_status'), function ($query) use ($request) {
                $slug = (string) $request->string('project_status');
                $query->whereHas('status', fn ($nested) => $nested->where('slug', $slug));
            })
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get();

        $payload = $projects->map(function (Project $project) {
            $categories = $project->categories->map(function ($category) {
                return [
                    'id' => (int) $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'meta_title' => $category->meta_title,
                    'meta_description' => $category->meta_description,
                    'sort_order' => (int) $category->sort_order,
                    'is_active' => $category->is_active ? '1' : '0',
                    'created_at' => optional($category->created_at)?->toISOString(),
                    'updated_at' => optional($category->updated_at)?->toISOString(),
                ];
            })->values();

            $content = $project->sliders->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'project_id' => (int) $item->project_id,
                    'sort_order' => (int) $item->sort_order,
                    'description' => $item->description,
                    'image' => $item->image,
                    'video' => $item->video,
                    'type' => $item->type,
                    'width' => $item->width,
                    'created_at' => optional($item->created_at)?->toISOString(),
                    'updated_at' => optional($item->updated_at)?->toISOString(),
                ];
            })->values();

            return [
                'id' => (int) $project->id,
                'tag_id' => $project->tag_id,
                'category_id' => $project->categories->pluck('id')->implode(','),
                'category' => $categories,
                'project_name' => $project->project_name,
                'client_name' => $project->client_name,
                'slug' => $project->slug,
                'project_thumbnail' => $project->project_thumbnail,
                'project_address' => $project->project_address,
                'project_main_image' => $project->project_main_image,
                'site_area' => $project->site_area,
                'built_up_area' => $project->built_up_area,
                'is_active' => (bool) $project->is_active,
                'created_at' => optional($project->created_at)?->toISOString(),
                'updated_at' => optional($project->updated_at)?->toISOString(),
                'project_status' => $project->status?->name,
                'tag' => $project->tag ? [
                    'id' => (int) $project->tag->id,
                    'name' => $project->tag->name,
                    'slug' => $project->tag->slug,
                    'sort_order' => (int) $project->tag->sort_order,
                    'is_active' => (bool) $project->tag->is_active,
                    'created_at' => optional($project->tag->created_at)?->toISOString(),
                    'updated_at' => optional($project->tag->updated_at)?->toISOString(),
                ] : null,
                'content' => $content,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Projects fetched successfully.',
            'data' => $payload,
        ]);
    }
}
