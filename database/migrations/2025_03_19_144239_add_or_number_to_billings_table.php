<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('or_number')->nullable()->after('paymentDate'); // Add OR number
        });
    }
    
    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('or_number');
        });
    }
    
};
