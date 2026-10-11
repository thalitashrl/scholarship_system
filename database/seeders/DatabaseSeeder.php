<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TestingDataSeeder::class,
        ]);

        // Hapus cache statistik dasbor agar data baru langsung tampil
        Cache::forget('admin_dashboard_stats');

        $this->command->info('===================================================');
        $this->command->info('   SCHOLARSHIP SYSTEM SEEDER COMPLETED SUCCESSFULLY!');
        $this->command->info('===================================================');
        $this->command->info('Kredensial Akun untuk Pengujian:');
        $this->command->info(' - Admin:     admin@test.com     | password');
        $this->command->info(' - Staff:     staff@test.com     | password');
        $this->command->info(' - WD3:       wd3@test.com       | password');
        $this->command->info(' - Mahasiswa: mahasiswa@test.com | password');
        $this->command->info('===================================================');
    }
}
