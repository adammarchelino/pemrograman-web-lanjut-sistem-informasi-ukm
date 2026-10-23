<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('ukm_id')->constrained('ukm')->restrictOnDelete();
            $table->json('jawaban_formulir');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->dateTime('diajukan_pada');
            $table->text('catatan_pengurus')->nullable();
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('diputuskan_pada')->nullable();
            $table->unsignedTinyInteger('pengajuan_aktif_key')->nullable()->virtualAs(
                "CASE WHEN `status` IN ('pending', 'diterima') THEN 1 ELSE NULL END"
            );
            $table->timestamps();

            $table->unique(['user_id', 'ukm_id', 'pengajuan_aktif_key'], 'pendaftaran_anggota_aktif_unique');
            $table->index(['user_id', 'status']);
            $table->index(['ukm_id', 'status', 'diajukan_pada'], 'pendaftaran_anggota_ukm_status_waktu_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_anggota');
    }
};
