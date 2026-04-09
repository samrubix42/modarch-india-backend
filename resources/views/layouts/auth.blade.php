<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ $title ?? 'Admin Login' }}</title>

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">
		@vite(['resources/css/app.css', 'resources/js/app.js'])
		@livewireStyles
	</head>
	<body class="min-h-screen bg-emerald-50 text-slate-800 antialiased">
		{{ $slot }}
		@livewireScripts
	</body>
</html>
