<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Featured',
            'Mixed Use',
            'Commercial',
            'Residential',
            'Hospitality',
            'Interiors',
            'Institutional',
            'Miscellaneous',
        ];

        foreach ($categories as $index => $name) {
            $slug = Str::slug($name);

            ProjectCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'meta_title' => $name,
                    'meta_description' => null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
