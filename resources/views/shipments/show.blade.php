@extends('layouts.app')
@section('title', $shipment->tracking_number)
@section('content')
@include('shipments._detail', ['shipment' => $shipment, 'admin' => true])
@endsection
