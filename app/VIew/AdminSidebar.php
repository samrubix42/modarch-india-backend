<?php

namespace App\VIew;

use App\Views\Builders\AdminSidebar as SidebarBuilder;
use Illuminate\Support\Collection;

class AdminSidebar
{
	/**
	 * Legacy compatibility wrapper for older imports.
	 */
	public static function menu(mixed $user = null): Collection
	{
		return SidebarBuilder::menu($user)->get();
	}
}
