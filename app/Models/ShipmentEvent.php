<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    protected $fillable = ['shipment_id', 'status', 'location', 'note', 'lat', 'lng', 'occurred_at'];

    protected $casts = ['lat' => 'float', 'lng' => 'float', 'occurred_at' => 'datetime'];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getLabelAttribute(): string
    {
        return Shipment::STATUSES[$this->status] ?? '-';
    }
}
