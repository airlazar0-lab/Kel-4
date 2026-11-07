@php $cur = $shipment->status; @endphp
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded shadow p-4">
            <div class="flex justify-between flex-wrap gap-2">
                <div>
                    <div class="font-bold text-lg">{{ $shipment->tracking_number }}</div>
                    <div class="text-sm text-slate-500">{{ $shipment->item_name }}</div>
                </div>
                <span class="self-start rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-semibold">{{ $shipment->status_label }}</span>
            </div>
            <div class="flex my-4 overflow-x-auto">
                @foreach (\App\Models\Shipment::STATUSES as $i => $label)
                    <div class="flex-1 min-w-[84px] text-center text-xs {{ $i <= $cur ? 'text-slate-800' : 'text-slate-400' }}">
                        <div class="mx-auto mb-1 h-5 w-5 rounded-full {{ $i <= $cur ? 'bg-emerald-600' : 'bg-slate-300' }}"></div>{{ $label }}
                    </div>
                @endforeach
            </div>
            <div id="map" class="h-80 rounded"></div>
            <div class="grid grid-cols-3 gap-2 mt-3 text-sm">
                <div class="bg-slate-50 rounded p-2">Jarak total<b class="block">{{ $shipment->distance_km }} km</b></div>
                <div class="bg-slate-50 rounded p-2">Sisa jarak<b class="block">{{ $shipment->remaining_km }} km</b></div>
                <div class="bg-slate-50 rounded p-2">Estimasi tiba<b class="block">{{ $cur >= 5 ? 'Selesai' : $shipment->eta_hours.' jam' }}</b></div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="font-semibold mb-3">Riwayat perjalanan</h2>
            <ul class="border-l-2 border-slate-200 pl-4 space-y-3">
                @foreach ($shipment->events as $e)
                    <li>
                        <b>{{ $e->note ?: $e->label }}</b>
                        <div class="text-sm text-slate-500">{{ $e->location }} · {{ $e->occurred_at->translatedFormat('d M H:i') }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded shadow p-4">
            <h2 class="font-semibold mb-1">Alamat tujuan</h2>
            <b>{{ $shipment->recipient_name }}</b>
            <div class="text-sm text-slate-500">{{ $shipment->recipient_phone }}</div>
            <div>{{ $shipment->address }}, {{ $shipment->dest_city }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $shipment->dest_lat }}, {{ $shipment->dest_lng }}</div>
        </div>

        @if ($admin)
        <form method="POST" action="{{ route('shipments.events.store', $shipment) }}" class="bg-white rounded shadow p-4 space-y-2">
            @csrf
            <h2 class="font-semibold">Tambah status</h2>
            <select name="status" class="w-full rounded border px-3 py-2">
                @foreach (\App\Models\Shipment::STATUSES as $i => $label)
                    <option value="{{ $i }}" @selected($i === min($cur + 1, 5))>{{ $label }}</option>
                @endforeach
            </select>
            <input name="location" placeholder="Lokasi (mis. Hub Cirebon)" class="w-full rounded border px-3 py-2">
            <input name="note" placeholder="Catatan (opsional)" class="w-full rounded border px-3 py-2">
            <div class="grid grid-cols-2 gap-2">
                <input id="ev_lat" name="lat" placeholder="Latitude" class="rounded border px-3 py-2">
                <input id="ev_lng" name="lng" placeholder="Longitude" class="rounded border px-3 py-2">
            </div>
            <p class="text-xs text-slate-500">Klik peta untuk mengisi posisi paket saat ini.</p>
            <button class="w-full rounded bg-emerald-700 text-white py-2">Simpan status</button>
        </form>

        <div class="bg-white rounded shadow p-4 flex gap-2">
            <a href="{{ route('shipments.edit', $shipment) }}" class="flex-1 text-center rounded bg-blue-600 text-white py-2">Ubah data</a>
            <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" class="flex-1"
                  onsubmit="return confirm('Hapus paket ini?')">
                @csrf @method('DELETE')
                <button class="w-full rounded bg-red-600 text-white py-2">Hapus</button>
            </form>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const origin = [{{ $shipment->origin_lat }}, {{ $shipment->origin_lng }}];
const dest = [{{ $shipment->dest_lat }}, {{ $shipment->dest_lng }}];
const now = @json($shipment->current_position);
const path = @json($shipment->path);
const delivered = {{ $cur >= 5 ? 'true' : 'false' }};

const map = L.map('map');
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(map);

L.polyline([origin, dest], {color: '#94a3b8', weight: 3, dashArray: '8 8'}).addTo(map);
L.polyline(path, {color: '#059669', weight: 4}).addTo(map);
L.circleMarker(origin, {color: '#111', radius: 6}).addTo(map).bindTooltip('Asal: {{ $shipment->origin_city }}');
L.marker(dest).addTo(map).bindTooltip('Tujuan: {{ $shipment->dest_city }}');
if (!delivered) L.circleMarker(now, {color: '#059669', fillOpacity: .9, radius: 9}).addTo(map).bindTooltip('Posisi paket');
map.fitBounds(L.latLngBounds([origin, dest, now]).pad(0.25));

@if ($admin)
map.on('click', (e) => {
    document.getElementById('ev_lat').value = e.latlng.lat.toFixed(6);
    document.getElementById('ev_lng').value = e.latlng.lng.toFixed(6);
});
@endif
</script>
@endpush
