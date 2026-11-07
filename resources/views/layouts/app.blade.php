<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lacak Paket')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<header class="bg-emerald-700 text-white">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('track') }}" class="font-bold">Lacak Paket</a>
        <nav class="flex gap-4 text-sm">
            <a href="{{ route('track') }}">Lacak resi</a>
            <a href="{{ route('shipments.index') }}">Admin</a>
        </nav>
    </div>
</header>
<main class="max-w-5xl mx-auto px-4 py-6">
    @if (session('ok'))
        <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-2">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
