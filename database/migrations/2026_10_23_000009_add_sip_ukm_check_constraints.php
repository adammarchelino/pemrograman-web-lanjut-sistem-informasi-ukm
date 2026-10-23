<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CHECKS = [
        ['ukm', 'ukm_oprec_window_check', 'pendaftaran_anggota_dibuka_pada IS NULL OR pendaftaran_anggota_ditutup_pada IS NULL OR pendaftaran_anggota_ditutup_pada > pendaftaran_anggota_dibuka_pada'],
        ['kegiatan', 'kegiatan_capacity_positive_check', 'kapasitas > 0'],
        ['kegiatan', 'kegiatan_time_order_check', 'selesai_pada > mulai_pada'],
        ['kegiatan', 'kegiatan_registration_window_check', 'pendaftaran_dibuka_pada IS NULL OR pendaftaran_ditutup_pada IS NULL OR pendaftaran_ditutup_pada > pendaftaran_dibuka_pada'],
    ];

    public function up(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getConfig('driver');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $version = $connection->selectOne('select version() as version')->version;
        $isMariaDb = stripos($version, 'mariadb') !== false;
        preg_match($isMariaDb ? '/(\d+\.\d+\.\d+)-MariaDB/i' : '/(\d+\.\d+\.\d+)/', $version, $matches);
        $minimumVersion = $isMariaDb ? '10.2.1' : '8.0.16';

        if (! isset($matches[1]) || version_compare($matches[1], $minimumVersion, '<')) {
            $product = $isMariaDb ? 'MariaDB' : 'MySQL';

            throw new RuntimeException("SIP-UKM CHECK constraints require {$product} {$minimumVersion}+.");
        }

        foreach (self::CHECKS as [$table, $name, $expression]) {
            $connection->statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$name}` CHECK ({$expression})");
        }
    }

    public function down(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getConfig('driver');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        foreach (array_reverse(self::CHECKS) as [$table, $name]) {
            $drop = $driver === 'mariadb' ? 'DROP CONSTRAINT' : 'DROP CHECK';
            $connection->statement("ALTER TABLE `{$table}` {$drop} `{$name}`");
        }
    }
};
