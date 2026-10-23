<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukm', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->index();
            $table->string('logo')->nullable();
            $table->text('deskripsi');
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('email_kontak')->nullable();
            $table->string('nomor_kontak', 30)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif')->index();
            $table->enum('status_pendaftaran_anggota', ['dibuka', 'ditutup'])->default('ditutup');
            $table->dateTime('pendaftaran_anggota_dibuka_pada')->nullable();
            $table->dateTime('pendaftaran_anggota_ditutup_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukm');
    }
};
