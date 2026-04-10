<?php

namespace App\Views\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class AdminSidebar
{
    public function __construct(mixed $user)
    {
        // Kept for API compatibility with existing sidebar calls.
    }

    public static function menu(mixed $user): self
    {
        return new self($user);
    }

    public function get(): Collection
    {
        $current = request()->route()?->getName() ?: '';

        return collect([
            (object) [
                'title' => 'Dashboard',
                'icon' => 'ri-home-5-line',
                'routeName' => 'admin.dashboard',
                'url' => $this->routeIfExists('admin.dashboard'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
            (object) [
                'title' => 'Project Management',
                'icon' => 'ri-shopping-bag-3-line',
                'url' => '#',
                'hasSubmenu' => true,
                'submenu' => [
                    (object) [
                        'title' => 'Projects',
                        'routeName' => 'admin.projects',
                        'url' => $this->routeIfExists('admin.projects'),
                    ],
                    (object) [
                        'title' => 'Project Categories',
                        'routeName' => 'admin.project-categories',
                        'url' => $this->routeIfExists('admin.project-categories'),
                    ],
                    (object) [
                        'title' => 'Project Statuses',
                        'routeName' => 'admin.project-statuses',
                        'url' => $this->routeIfExists('admin.project-statuses'),
                    ],
                    (object) [
                        'title' => 'Project Tags',
                        'routeName' => 'admin.project-tags',
                        'url' => $this->routeIfExists('admin.project-tags'),
                    ],
                 
                ],
            ],
            (object) [
                'title' => 'Jobs',
                'icon' => 'ri-briefcase-4-line',
                'url' => '#',
                'hasSubmenu' => true,
                'submenu' => [
                    (object) [
                        'title' => 'Job Profiles',
                        'routeName' => 'admin.job-profiles',
                        'url' => $this->routeIfExists('admin.job-profiles'),
                    ],
                    (object) [
                        'title' => 'Applied Jobs',
                        'routeName' => 'admin.job-applied-list',
                        'url' => $this->routeIfExists('admin.job-applied-list'),
                    ],
                ],
            ],
            (object) [
                'title' => 'Settings',
                'icon' => 'ri-settings-3-line',
                'routeName' => 'admin.settings',
                'url' => $this->routeIfExists('admin.settings'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
            (object) [
                'title' => 'Contacts',
                'icon' => 'ri-contacts-line',
                'routeName' => 'admin.contacts',
                'url' => $this->routeIfExists('admin.contacts'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
        ])->map(function (object $item) use ($current): object {
            $item->key = str($item->title)->slug()->toString();
            $item->active = false;
            $item->open = false;

            if ($item->hasSubmenu) {
                $item->submenu = collect($item->submenu)->map(function (object $child) use ($current): object {
                    $child->active = isset($child->routeName) && str($current)->is($child->routeName . '*');

                    return $child;
                })->filter(fn (object $child): bool => $child->url !== '#')->values()->all();

                $item->active = collect($item->submenu)->contains(fn (object $child): bool => (bool) $child->active);
                $item->open = $item->active;

                return $item;
            }

            $item->active = isset($item->routeName) && str($current)->is($item->routeName . '*');

            return $item;
        })->filter(function (object $item): bool {
            if ($item->hasSubmenu) {
                return ! empty($item->submenu);
            }

            return $item->url !== '#';
        })->values();
    }

    private function routeIfExists(string $name): string
    {
        return Route::has($name) ? route($name) : '#';
    }

}
