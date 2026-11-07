@extends('layouts.app')
@section('title', 'Daftar pengiriman')
@section('content')
<div class="flex flex-wrap gap-2 justify-between mb-4">
    <form method="GET" class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Cari resi, penerima, kota" class="rounded border px-3 py-2">
        <button class="rounded bg-slate-700 text-white px-4">Cari</button>
    </form>
    <a href="{{ route('shipments.create') }}" class="rounded bg-emerald-700 text-white px-4 py-2">+ Paket baru</a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left">
        <tr><th class="p-3">Resi</th><th class="p-3">Penerima</th><th class="p-3">Rute</th><th class="p-3">Status</th><th class="p-3"></th></tr>
    </thead>
    <tbody>
    @forelse ($shipments as $s)
        <tr class="border-t">
            <td class="p-3 font-medium">{{ $s->tracking_number }}</td>
            <td class="p-3">{{ $s->recipient_name }}</td>
            <td class="p-3">{{ $s->origin_city }} ke {{ $s->dest_city }} ({{ $s->distance_km }} km)</td>
            <td class="p-3"><span class="rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5">{{ $s->status_label }}</span></td>
            <td class="p-3 whitespace-nowrap">
                <a class="text-emerald-700" href="{{ route('shipments.show', $s) }}">Lihat</a> ·
                <a class="text-blue-700" href="{{ route('shipments.edit', $s) }}">Ubah</a> ·
                <form method="POST" action="{{ route('shipments.destroy', $s) }}" class="inline"
                      onsubmit="return confirm('Hapus paket {{ $s->tracking_number }}?')">
                    @csrf @method('DELETE')
                    <button class="text-red-700">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="p-6 text-center text-slate-500">Belum ada paket.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $shipments->links() }}</div>
@endsection
