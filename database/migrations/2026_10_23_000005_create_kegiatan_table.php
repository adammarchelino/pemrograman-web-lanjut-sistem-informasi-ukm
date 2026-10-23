<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukm_id')->constrained('ukm')->restrictOnDelete();
            $table->string('nama', 150);
            $table->text('deskripsi');
            $table->dateTime('mulai_pada');
            $table->dateTime('selesai_pada');
            $table->string('lokasi', 255);
            $table->unsignedInteger('kapasitas');
            $table->enum('audiens', ['mahasiswa_poliwangi', 'umum']);
            $table->dateTime('pendaftaran_dibuka_pada')->nullable();
            $table->dateTime('pendaftaran_ditutup_pada')->nullable();
            $table->enum('status', ['draf', 'dipublikasikan', 'dibatalkan', 'selesai'])->default('draf');
            $table->timestamps();

            $table->index(['ukm_id', 'status', 'mulai_pada'], 'kegiatan_ukm_status_mulai_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
