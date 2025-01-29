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
        Schema::table('billings', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['clientId']);

            // Modify the column to allow null values
            $table->unsignedBigInteger('clientId')->nullable()->change();

            // Add the foreign key constraint back with ON DELETE SET NULL
            $table->foreign('clientId')
                ->references('id')
                ->on('clients')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['clientId']);

            // Add back a foreign key constraint without ON DELETE SET NULL
            $table->foreign('clientId')
                ->references('id')
                ->on('clients');

            // Only after removing the SET NULL constraint can we make the column NOT NULL
            $table->unsignedBigInteger('clientId')->nullable(false)->change();
        });
    }
};
