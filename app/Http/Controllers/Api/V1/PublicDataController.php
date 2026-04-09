<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Models\Setting;
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
}
