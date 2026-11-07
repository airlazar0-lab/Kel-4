<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->string('tracking_number')->unique();
            $t->string('item_name');
            $t->string('recipient_name');
            $t->string('recipient_phone', 30)->nullable();
            $t->text('address');
            $t->string('origin_city');
            $t->string('dest_city');
            $t->decimal('origin_lat', 10, 7);
            $t->decimal('origin_lng', 10, 7);
            $t->decimal('dest_lat', 10, 7);
            $t->decimal('dest_lng', 10, 7);
            $t->unsignedTinyInteger('status')->default(0);
            $t->timestamps();
        });

        Schema::create('shipment_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('status');
            $t->string('location');
            $t->string('note')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->timestamp('occurred_at')->useCurrent();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipments');
    }
};
