<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keanggotaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('ukm_id')->constrained('ukm')->restrictOnDelete();
            $table->foreignId('pendaftaran_anggota_id')->unique()->constrained('pendaftaran_anggota')->restrictOnDelete();
            $table->date('tanggal_bergabung');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->unique(['user_id', 'ukm_id'], 'keanggotaan_user_ukm_unique');
            $table->index(['ukm_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keanggotaan');
    }
};
