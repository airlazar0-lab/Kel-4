@extends('layouts.app')
@section('title', $shipment->exists ? 'Ubah paket' : 'Paket baru')
@section('content')
@php
    $f = fn ($k) => old($k, $shipment->$k);
    $input = 'w-full rounded border px-3 py-2';
@endphp
<h1 class="text-xl font-bold mb-4">{{ $shipment->exists ? 'Ubah paket '.$shipment->tracking_number : 'Paket baru' }}</h1>

<form method="POST" class="grid md:grid-cols-2 gap-6"
      action="{{ $shipment->exists ? route('shipments.update', $shipment) : route('shipments.store') }}">
    @csrf
    @if ($shipment->exists) @method('PUT') @endif

    <div class="bg-white rounded shadow p-4 space-y-3">
        <label class="block text-sm">Nama barang<input name="item_name" value="{{ $f('item_name') }}" class="{{ $input }}"></label>
        <label class="block text-sm">Penerima<input name="recipient_name" value="{{ $f('recipient_name') }}" class="{{ $input }}"></label>
        <label class="block text-sm">Telepon<input name="recipient_phone" value="{{ $f('recipient_phone') }}" class="{{ $input }}"></label>
        <label class="block text-sm">Alamat lengkap<textarea id="address" name="address" rows="2" class="{{ $input }}">{{ $f('address') }}</textarea></label>
        <div class="grid grid-cols-2 gap-3">
            <label class="block text-sm">Kota asal<input name="origin_city" value="{{ $f('origin_city') }}" class="{{ $input }}"></label>
            <label class="block text-sm">Kota tujuan<input name="dest_city" value="{{ $f('dest_city') }}" class="{{ $input }}"></label>
            <label class="block text-sm">Latitude asal<input id="origin_lat" name="origin_lat" value="{{ $f('origin_lat') }}" class="{{ $input }}"></label>
            <label class="block text-sm">Longitude asal<input id="origin_lng" name="origin_lng" value="{{ $f('origin_lng') }}" class="{{ $input }}"></label>
            <label class="block text-sm">Latitude tujuan<input id="dest_lat" name="dest_lat" value="{{ $f('dest_lat') }}" class="{{ $input }}"></label>
            <label class="block text-sm">Longitude tujuan<input id="dest_lng" name="dest_lng" value="{{ $f('dest_lng') }}" class="{{ $input }}"></label>
        </div>
        <div class="flex gap-2">
            <button class="rounded bg-emerald-700 text-white px-5 py-2">Simpan</button>
            <a href="{{ route('shipments.index') }}" class="rounded bg-slate-200 px-5 py-2">Batal</a>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4 space-y-3">
        <div class="flex flex-wrap gap-3 items-center text-sm">
            <label><input type="radio" name="mode" value="dest" checked> Klik peta = tujuan</label>
            <label><input type="radio" name="mode" value="origin"> Klik peta = asal</label>
            <button type="button" id="geocode" class="rounded bg-slate-700 text-white px-3 py-1">Cari koordinat dari alamat</button>
        </div>
        <div id="map" class="h-96 rounded"></div>
        <p id="gmsg" class="text-sm text-slate-500"></p>
    </div>
</form>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
const num = (id, d) => parseFloat($(id).value) || d;
const map = L.map('map').setView([num('dest_lat', -6.9), num('dest_lng', 107.6)], 8);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(map);

const origin = L.circleMarker([num('origin_lat', -6.2), num('origin_lng', 106.8)], {color: '#111', radius: 7}).addTo(map);
const dest = L.marker([num('dest_lat', -6.9), num('dest_lng', 107.6)]).addTo(map);

function setPoint(kind, lat, lng) {
    lat = +lat.toFixed(6); lng = +lng.toFixed(6);
    $(kind + '_lat').value = lat; $(kind + '_lng').value = lng;
    (kind === 'dest' ? dest : origin).setLatLng([lat, lng]);
}
map.on('click', (e) => setPoint(document.querySelector('[name=mode]:checked').value, e.latlng.lat, e.latlng.lng));

// Geocoding alamat via Nominatim (OpenStreetMap). Patuhi kebijakan penggunaannya; untuk produksi pakai penyedia sendiri.
$('geocode').onclick = async () => {
    const q = $('address').value.trim();
    if (!q) { $('gmsg').textContent = 'Isi alamat dulu.'; return; }
    $('gmsg').textContent = 'Mencari...';
    try {
        const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q));
        const j = await r.json();
        if (!j.length) { $('gmsg').textContent = 'Alamat tidak ditemukan. Klik peta untuk menandai manual.'; return; }
        setPoint('dest', parseFloat(j[0].lat), parseFloat(j[0].lon));
        map.setView([j[0].lat, j[0].lon], 14);
        $('gmsg').textContent = 'Koordinat tujuan diisi dari alamat.';
    } catch (e) { $('gmsg').textContent = 'Gagal menghubungi layanan geocoding.'; }
};
</script>
@endpush
