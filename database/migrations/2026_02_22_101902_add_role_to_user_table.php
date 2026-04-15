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
            $table->enum('role', ['admin', 'kepala_sekolah', 'koordinator', 'petugas', 'user'])
                  ->default('user')
                  ->after('email');
            
            $table->string('department', 50)->nullable()->after('role'); 
            // TU, Sarpras, BK, Kurikulum, Kesiswaan, dll
            
            $table->string('telp', 20)->nullable()->after('department');
            $table->text('alamat')->nullable()->after('telp');
            $table->boolean('is_active')->default(true)->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'telp', 'alamat', 'is_active']);
        });
    }
};