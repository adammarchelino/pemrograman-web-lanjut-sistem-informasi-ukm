<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi_ukm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukm_id')->constrained('ukm')->restrictOnDelete();
            $table->string('nama', 100);
            $table->timestamps();

            $table->unique(['ukm_id', 'nama']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('akun_aktif')->default(true)->after('role')->index();
            $table->enum('jabatan_ukm', ['ketua', 'inti', 'divisi'])->nullable()->after('akun_aktif');
            $table->foreignId('divisi_ukm_id')->nullable()->after('ukm_id')->constrained('divisi_ukm')->restrictOnDelete();
            $table->boolean('wajib_ganti_password')->default(false)->after('divisi_ukm_id');
            $table->index(['ukm_id', 'jabatan_ukm', 'akun_aktif'], 'users_ukm_jabatan_aktif_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['divisi_ukm_id']);
            $table->dropIndex('users_ukm_jabatan_aktif_index');
            $table->dropIndex(['akun_aktif']);
            $table->dropColumn(['akun_aktif', 'jabatan_ukm', 'divisi_ukm_id', 'wajib_ganti_password']);
        });

        Schema::dropIfExists('divisi_ukm');
    }
};
