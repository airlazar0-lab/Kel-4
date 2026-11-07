<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => 'required|string|max:150',
            'recipient_name' => 'required|string|max:120',
            'recipient_phone' => 'nullable|string|max:30',
            'address' => 'required|string|max:500',
            'origin_city' => 'required|string|max:80',
            'dest_city' => 'required|string|max:80',
            'origin_lat' => 'required|numeric|between:-90,90',
            'origin_lng' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lng' => 'required|numeric|between:-180,180',
        ];
    }

    public function attributes(): array
    {
        return [
            'item_name' => 'nama barang', 'recipient_name' => 'penerima', 'address' => 'alamat',
            'origin_city' => 'kota asal', 'dest_city' => 'kota tujuan',
            'origin_lat' => 'latitude asal', 'origin_lng' => 'longitude asal',
            'dest_lat' => 'latitude tujuan', 'dest_lng' => 'longitude tujuan',
        ];
    }
}
