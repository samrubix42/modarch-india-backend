<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Under Construction',
            'Completed',
            'Partially Completed',
            'Concept',
            'Under Development',
        ];

        foreach ($statuses as $index => $name) {
            $slug = Str::slug($name);

            ProjectStatus::updateOrCreate(
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
