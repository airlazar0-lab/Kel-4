# Lacak Paket (Laravel 11/12) — CRUD + GIS

Fitur: CRUD pengiriman, status bertahap, peta Leaflet + OpenStreetMap, klik peta untuk
mengisi koordinat, geocoding alamat (Nominatim), jarak Haversine, estimasi tiba, endpoint GeoJSON.

## Pasang
1. Buat proyek baru:  `composer create-project laravel/laravel lacak-paket && cd lacak-paket`
2. Salin isi folder zip ini ke dalam proyek (gabungkan `app/`, `database/`, `resources/`, `routes/`;
   timpa `routes/web.php`).
3. Atur database di `.env` (SQLite bawaan Laravel sudah cukup untuk mencoba).
4. Di `database/seeders/DatabaseSeeder.php`, dalam `run()` tambahkan:
   `$this->call(ShipmentSeeder::class);`
5. Jalankan:
   ```
   php artisan migrate --seed
   php artisan serve
   ```
6. Buka `http://127.0.0.1:8000/lacak?resi=ID2611070001` (publik) dan `http://127.0.0.1:8000/admin/shipments` (admin).

## Rute
| Method | URL | Fungsi |
|---|---|---|
| GET | /lacak?resi= | Lacak publik |
| GET | /admin/shipments | Daftar + cari |
| GET/POST | /admin/shipments/create, /admin/shipments | Tambah |
| GET | /admin/shipments/{id} | Detail + peta |
| GET/PUT | /admin/shipments/{id}/edit, /admin/shipments/{id} | Ubah |
| DELETE | /admin/shipments/{id} | Hapus |
| POST | /admin/shipments/{id}/events | Tambah status + posisi |
| GET | /admin/shipments/{id}/geojson | Data GeoJSON |

## Catatan produksi
- Rute `/admin` belum diproteksi. Pasang Laravel Breeze (`composer require laravel/breeze --dev`)
  lalu tambahkan `->middleware('auth')` pada grup admin.
- Nominatim publik hanya untuk pemakaian ringan (maks. 1 permintaan/detik, wajib atribusi).
  Untuk produksi gunakan penyedia geocoding berbayar atau instal sendiri.
- Tile OSM publik juga punya batasan pemakaian; untuk trafik besar pakai penyedia tile.
- Koordinat disimpan sebagai kolom decimal. Jika butuh query spasial (radius, poligon wilayah),
  pindah ke MySQL spatial atau PostgreSQL + PostGIS.
- Estimasi tiba memakai jarak garis lurus dan kecepatan 40 km/jam; ganti dengan API routing
  (OSRM, Google Directions) bila ingin akurat mengikuti jalan.
