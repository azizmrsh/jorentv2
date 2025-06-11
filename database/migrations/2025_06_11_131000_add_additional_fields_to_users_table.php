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
        Schema::table('users', function (Blueprint $table) {
            // إضافة الحقول الإضافية المطلوبة
            $table->string('midname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after('midname');
            $table->date('birth_date')->nullable()->after('lastname');
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('profile_photo')->nullable()->after('address');
            $table->enum('role', ['admin', 'manager', 'user', 'owner'])->default('user')->after('profile_photo');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'midname',
                'lastname', 
                'birth_date',
                'phone',
                'address',
                'profile_photo',
                'role',
                'status'
            ]);
        });
    }
};
