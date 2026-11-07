<?php

namespace App\Models;

use App\Support\Geo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shipment extends Model
{
    public const STATUSES = [
        'Pesanan dibuat', 'Dikemas', 'Diserahkan ke kurir',
        'Transit di hub', 'Dalam pengantaran', 'Diterima',
    ];

    protected $fillable = [
        'tracking_number', 'item_name', 'recipient_name', 'recipient_phone', 'address',
        'origin_city', 'dest_city', 'origin_lat', 'origin_lng', 'dest_lat', 'dest_lng', 'status',
    ];

    protected $casts = [
        'origin_lat' => 'float', 'origin_lng' => 'float',
        'dest_lat' => 'float', 'dest_lng' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $s) {
            $s->tracking_number ??= 'ID' . now()->format('ymd') . strtoupper(Str::random(6));
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderByDesc('occurred_at')->orderByDesc('id');
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $q->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('tracking_number', 'like', "%$term%")
              ->orWhere('recipient_name', 'like', "%$term%")
              ->orWhere('dest_city', 'like', "%$term%")
              ->orWhere('origin_city', 'like', "%$term%");
        }));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? '-';
    }

    /** Posisi terkini: event terbaru yang punya koordinat, kalau tidak ada pakai titik asal. */
    public function getCurrentPositionAttribute(): array
    {
        $e = $this->events->first(fn ($e) => $e->lat !== null && $e->lng !== null);

        return $e ? [$e->lat, $e->lng] : [$this->origin_lat, $this->origin_lng];
    }

    public function getDistanceKmAttribute(): float
    {
        return round(Geo::haversine($this->origin_lat, $this->origin_lng, $this->dest_lat, $this->dest_lng), 1);
    }

    public function getRemainingKmAttribute(): float
    {
        if ($this->status >= 5) {
            return 0;
        }
        [$lat, $lng] = $this->current_position;

        return round(Geo::haversine($lat, $lng, $this->dest_lat, $this->dest_lng), 1);
    }

    /** Estimasi sederhana dengan kecepatan rata-rata 40 km/jam. */
    public function getEtaHoursAttribute(): int
    {
        return $this->status >= 5 ? 0 : max(1, (int) ceil($this->remaining_km / 40));
    }

    /** Jalur untuk peta: asal, lalu semua titik event berkoordinat (urut waktu). */
    public function getPathAttribute(): array
    {
        $pts = [[$this->origin_lat, $this->origin_lng]];
        foreach ($this->events->reverse() as $e) {
            if ($e->lat !== null && $e->lng !== null) {
                $pts[] = [$e->lat, $e->lng];
            }
        }

        return $pts;
    }
}
