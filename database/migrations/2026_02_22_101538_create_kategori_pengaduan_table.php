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
        Schema::create('kategori_pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('icon', 50)->default('📝'); // emoji atau class icon
            $table->string('warna', 20)->default('gray'); // untuk UI
            $table->integer('sla_hours')->default(48); // target penyelesaian dalam jam
            $table->foreignId('pic_default_id')->nullable()->constrained('users')->nullOnDelete(); // PIC default
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0); // untuk sorting
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pengaduan');
    }
};