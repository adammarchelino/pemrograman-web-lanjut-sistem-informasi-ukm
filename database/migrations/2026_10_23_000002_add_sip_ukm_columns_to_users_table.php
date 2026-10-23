<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim', 20)->nullable()->unique();
            $table->enum('role', ['mahasiswa', 'pengurus_ukm', 'kemahasiswaan'])->default('mahasiswa')->index();
            $table->foreignId('ukm_id')->nullable()->constrained('ukm')->restrictOnDelete();
            $table->index(['role', 'ukm_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ukm_id']);
            $table->dropIndex(['role', 'ukm_id']);
            $table->dropIndex(['role']);
            $table->dropUnique(['nim']);
            $table->dropColumn(['ukm_id', 'role', 'nim']);
        });
    }
};
