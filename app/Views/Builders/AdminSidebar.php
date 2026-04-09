<?php

namespace App\Views\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class AdminSidebar
{
    protected mixed $user;

    public function __construct(mixed $user)
    {
        $this->user = $user;
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
                'title' => 'Product Management',
                'icon' => 'ri-shopping-bag-3-line',
                'url' => '#',
                'hasSubmenu' => true,
                'submenu' => [
                    (object) [
                        'title' => 'Categories',
                        'routeName' => 'admin.categories',
                        'url' => $this->routeIfExists('admin.categories'),
                    ],
                    (object) [
                        'title' => 'Products',
                        'routeName' => 'admin.products.index',
                        'url' => $this->routeIfExists('admin.products.index'),
                    ],
                    (object) [
                        'title' => 'Inventory',
                        'routeName' => 'admin.inventory',
                        'url' => $this->routeIfExists('admin.inventory'),
                    ],
                ],
            ],
            (object) [
                'title' => 'Users',
                'icon' => 'ri-user-line',
                'routeName' => 'admin.users',
                'url' => $this->routeIfExists('admin.users'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
            (object) [
                'title' => 'Testimonials',
                'icon' => 'ri-chat-3-line',
                'routeName' => 'admin.testimonials',
                'url' => $this->routeIfExists('admin.testimonials'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
            (object) [
                'title' => 'Orders',
                'icon' => 'ri-shopping-cart-2-line',
                'routeName' => 'admin.orders',
                'url' => $this->routeIfExists('admin.orders'),
                'hasSubmenu' => false,
                'submenu' => [],
            ],
            (object) [
                'title' => 'Coupons',
                'icon' => 'ri-coupon-3-line',
                'routeName' => 'admin.coupons',
                'url' => $this->routeIfExists('admin.coupons'),
                'hasSubmenu' => false,
                'submenu' => [],
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
                'title' => 'Blog',
                'icon' => 'ri-newspaper-line',
                'url' => '#',
                'hasSubmenu' => true,
                'submenu' => [
                    (object) [
                        'title' => 'All Posts',
                        'routeName' => 'admin.blogs',
                        'url' => $this->routeIfExists('admin.blogs'),
                    ],
                    (object) [
                        'title' => 'Categories',
                        'routeName' => 'admin.blogs.categories',
                        'url' => $this->routeIfExists('admin.blogs.categories'),
                    ],
                ],
            ],
        ])->map(function (object $item) use ($current): object {
            $item->key = str($item->title)->slug()->toString();
            $item->active = false;
            $item->open = false;

            if ($item->hasSubmenu) {
                $item->submenu = collect($item->submenu)->map(function (object $child) use ($current): object {
                    $child->active = isset($child->routeName) && str($current)->is($child->routeName . '*');

                    return $child;
                })->all();

                $item->active = collect($item->submenu)->contains(fn (object $child): bool => (bool) $child->active);
                $item->open = $item->active;

                return $item;
            }

            $item->active = isset($item->routeName) && str($current)->is($item->routeName . '*');

            return $item;
        });
    }

    private function routeIfExists(string $name): string
    {
        return Route::has($name) ? route($name) : '#';
    }

}
