<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meterId')->constrained('meters');
            $table->decimal('reading', 10, 2);
            $table->decimal('consumption', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meterReadingId')->constrained('meter_readings');
            $table->decimal('rate', 10, 2);
            $table->decimal('totalAmount', 10, 2);
            $table->date('billingDate');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
        Schema::dropIfExists('meter_readings');
    }
};
