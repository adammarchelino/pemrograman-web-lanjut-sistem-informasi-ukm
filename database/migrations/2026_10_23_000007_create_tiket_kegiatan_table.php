<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_kegiatan_id')->unique()->constrained('registrasi_kegiatan')->restrictOnDelete();
            $table->string('kode_tiket', 64)->unique();
            $table->dateTime('diterbitkan_pada');
            $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_kegiatan');
    }
};
