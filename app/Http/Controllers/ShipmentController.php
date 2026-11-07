<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentRequest;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /* ---------- Publik: lacak berdasarkan resi ---------- */

    public function track(Request $request)
    {
        $resi = strtoupper(trim((string) $request->query('resi')));
        $shipment = $resi ? Shipment::with('events')->where('tracking_number', $resi)->first() : null;

        return view('track', ['resi' => $resi, 'shipment' => $shipment]);
    }

    /* ---------- Admin: CRUD ---------- */

    public function index(Request $request)
    {
        $shipments = Shipment::search($request->query('q'))->latest()->paginate(10)->withQueryString();

        return view('shipments.index', compact('shipments'));
    }

    public function create()
    {
        return view('shipments.form', ['shipment' => new Shipment([
            'origin_city' => 'Jakarta', 'origin_lat' => -6.2088, 'origin_lng' => 106.8456,
            'dest_city' => 'Bandung', 'dest_lat' => -6.9175, 'dest_lng' => 107.6191,
        ])]);
    }

    public function store(ShipmentRequest $request)
    {
        $shipment = Shipment::create($request->validated() + ['status' => 0]);
        $shipment->events()->create([
            'status' => 0, 'location' => $shipment->origin_city, 'note' => 'Pesanan dibuat',
            'lat' => $shipment->origin_lat, 'lng' => $shipment->origin_lng,
        ]);

        return redirect()->route('shipments.show', $shipment)->with('ok', 'Paket berhasil dibuat.');
    }

    public function show(Shipment $shipment)
    {
        $shipment->load('events');

        return view('shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {
        return view('shipments.form', compact('shipment'));
    }

    public function update(ShipmentRequest $request, Shipment $shipment)
    {
        $shipment->update($request->validated());

        return redirect()->route('shipments.show', $shipment)->with('ok', 'Data paket diperbarui.');
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')->with('ok', 'Paket dihapus.');
    }

    /* ---------- Admin: tambah status/posisi ---------- */

    public function storeEvent(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'status' => 'required|integer|between:0,5',
            'location' => 'required|string|max:150',
            'note' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180|required_with:lat',
        ]);

        $shipment->events()->create($data + ['occurred_at' => now()]);
        $shipment->update(['status' => $data['status']]);

        return back()->with('ok', 'Status ditambahkan.');
    }

    /* ---------- API GeoJSON untuk peta ---------- */

    public function geojson(Shipment $shipment)
    {
        $shipment->load('events');

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [
                ['type' => 'Feature', 'properties' => ['role' => 'origin'],
                    'geometry' => ['type' => 'Point', 'coordinates' => [$shipment->origin_lng, $shipment->origin_lat]]],
                ['type' => 'Feature', 'properties' => ['role' => 'destination'],
                    'geometry' => ['type' => 'Point', 'coordinates' => [$shipment->dest_lng, $shipment->dest_lat]]],
                ['type' => 'Feature', 'properties' => ['role' => 'route'],
                    'geometry' => ['type' => 'LineString',
                        'coordinates' => array_map(fn ($p) => [$p[1], $p[0]], $shipment->path)]],
            ],
        ]);
    }
}
