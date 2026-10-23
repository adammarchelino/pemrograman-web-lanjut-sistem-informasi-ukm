<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->enum('jenis_peserta', ['mahasiswa_poliwangi', 'umum']);
            $table->string('nama_peserta', 150);
            $table->string('email_peserta');
            $table->string('nomor_kontak', 30)->nullable();
            $table->string('nim', 20)->nullable();
            $table->enum('status', ['terdaftar', 'dibatalkan'])->default('terdaftar');
            $table->dateTime('terdaftar_pada');
            $table->unsignedBigInteger('user_aktif_key')->nullable()->virtualAs(
                "CASE WHEN `status` = 'terdaftar' AND `user_id` IS NOT NULL THEN `user_id` ELSE NULL END"
            );
            $table->string('email_aktif_key')->nullable()->virtualAs(
                "CASE WHEN `status` = 'terdaftar' AND `user_id` IS NULL THEN LOWER(TRIM(`email_peserta`)) ELSE NULL END"
            );
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_aktif_key'], 'registrasi_kegiatan_user_aktif_unique');
            $table->unique(['kegiatan_id', 'email_aktif_key'], 'registrasi_kegiatan_email_aktif_unique');
            $table->index(['kegiatan_id', 'status'], 'registrasi_kegiatan_kegiatan_status_index');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi_kegiatan');
    }
};
