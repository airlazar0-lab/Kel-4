<?php

namespace Database\Seeders;

use App\Models\Shipment;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $s = Shipment::create([
            'tracking_number' => 'ID2611070001', 'item_name' => 'Sepatu lari Trail X',
            'recipient_name' => 'Rina Kartika', 'recipient_phone' => '0812-3456-7890',
            'address' => 'Jl. Jenderal Sudirman No. 25, Lohbener, Indramayu',
            'origin_city' => 'Bandung', 'origin_lat' => -6.9175, 'origin_lng' => 107.6191,
            'dest_city' => 'Indramayu', 'dest_lat' => -6.3264, 'dest_lng' => 108.3200, 'status' => 4,
        ]);

        $rows = [
            [0, 'Bandung', 'Pesanan dibuat', -6.9175, 107.6191, 40],
            [2, 'Bandung', 'Kurir mengambil paket', -6.9175, 107.6191, 22],
            [3, 'Hub Cirebon', 'Tiba di hub transit', -6.7063, 108.5571, 6],
            [4, 'Indramayu', 'Kurir menuju alamat', -6.3500, 108.3000, 1],
        ];
        foreach ($rows as [$st, $loc, $note, $lat, $lng, $h]) {
            $s->events()->create([
                'status' => $st, 'location' => $loc, 'note' => $note,
                'lat' => $lat, 'lng' => $lng, 'occurred_at' => now()->subHours($h),
            ]);
        }
    }
}
