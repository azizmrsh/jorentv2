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
        Schema::table('contract1s', function (Blueprint $table) {
            $table->string('witness1_name')->nullable()->after('witness1_signature_path');
            $table->string('witness2_name')->nullable()->after('witness2_signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract1s', function (Blueprint $table) {
            $table->dropColumn(['witness1_name', 'witness2_name']);
        });
    }
};
