<?php

namespace Database\Seeders;

use App\Models\ProjectTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Infrastructure',
            'Mixed Use',
            'Interiors',
            'Commercial',
            'Residential',
            'Institutional',
            'Hospitality',
        ];

        foreach ($tags as $index => $name) {
            $slug = Str::slug($name);

            ProjectTag::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
