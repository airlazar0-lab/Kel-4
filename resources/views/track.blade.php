@extends('layouts.app')
@section('title', 'Lacak paket')
@section('content')
<form method="GET" action="{{ route('track') }}" class="flex gap-2 mb-6">
    <input name="resi" value="{{ $resi }}" placeholder="Masukkan nomor resi" class="flex-1 rounded border px-3 py-2">
    <button class="rounded bg-emerald-700 text-white px-5">Lacak</button>
</form>

@if ($resi && ! $shipment)
    <p class="text-red-700">Resi tidak ditemukan. Periksa kembali nomornya.</p>
@elseif ($shipment)
    @include('shipments._detail', ['shipment' => $shipment, 'admin' => false])
@else
    <p class="text-slate-500">Masukkan nomor resi untuk melihat posisi paket dan alamat tujuan.</p>
@endif
@endsection
