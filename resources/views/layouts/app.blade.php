<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
        <div class="relative min-h-screen" x-data="{ sidebarOpen: false }">
            <livewire:admin.include.sidebar />

            <div class="flex min-h-screen flex-col lg:pl-72">
                <livewire:admin.include.header :title="$title ?? null" />

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @include('components.toast')
        @livewireScripts
    </body>
</html>
