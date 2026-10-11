<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use App\Models\Beasiswa;
use App\Models\Surat;
use App\Models\Dokumen;
use App\Models\Consultation;
use App\Models\LiveChat;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class TestingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil atau Buat Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mahasiswaRole = Role::firstOrCreate(['name' => 'mahasiswa']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $wd3Role = Role::firstOrCreate(['name' => 'wd3']);

        // 2. Buat Pengguna
        // Admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
            ]
        );
        if (!$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Mahasiswa
        $mahasiswaUser = User::updateOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
            ]
        );
        if (!$mahasiswaUser->roles->contains($mahasiswaRole->id)) {
            $mahasiswaUser->roles()->attach($mahasiswaRole->id);
        }

        // Staff
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
            ]
        );
        if (!$staffUser->roles->contains($staffRole->id)) {
            $staffUser->roles()->attach($staffRole->id);
        }

        // WD3 (Wakil Dekan 3)
        $wd3User = User::updateOrCreate(
            ['email' => 'wd3@test.com'],
            [
                'name' => 'Dr. Hendra Wijaya',
                'password' => Hash::make('password'),
            ]
        );
        if (!$wd3User->roles->contains($wd3Role->id)) {
            $wd3User->roles()->attach($wd3Role->id);
        }

        // 3. Buat Profil Mahasiswa
        Mahasiswa::updateOrCreate(
            ['user_id' => $mahasiswaUser->id],
            [
                'nim' => '20260001',
                'prodi' => 'Informatika',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'angkatan' => 2026,
                'ipk' => 3.85,
            ]
        );

        // 4. Buat Beasiswa Dummy
        Beasiswa::updateOrCreate(
            ['judul' => 'Beasiswa Prestasi Akademik Internal'],
            [
                'jenis' => 'internal',
                'deskripsi' => 'Beasiswa berprestasi khusus mahasiswa aktif fakultas dengan IPK minimal 3.50.',
                'kuota' => 10,
                'deadline' => now()->addDays(30),
                'status' => 'aktif',
                'created_by' => $adminUser->id,
            ]
        );

        Beasiswa::updateOrCreate(
            ['judul' => 'Beasiswa Unggulan Eksternal Bank Indonesia'],
            [
                'jenis' => 'eksternal',
                'deskripsi' => 'Beasiswa bantuan biaya kuliah dari Bank Indonesia untuk mahasiswa semester 4 ke atas.',
                'kuota' => 5,
                'deadline' => now()->addDays(15),
                'status' => 'aktif',
                'created_by' => $adminUser->id,
            ]
        );

        Beasiswa::updateOrCreate(
            ['judul' => 'Beasiswa PPA Non-Aktif'],
            [
                'jenis' => 'internal',
                'deskripsi' => 'Peningkatan Prestasi Akademik yang masa pendaftarannya sudah lewat.',
                'kuota' => 2,
                'deadline' => now()->subDays(5),
                'status' => 'nonaktif',
                'created_by' => $adminUser->id,
            ]
        );

        // 5. Buat Pengajuan Surat
        $surat = Surat::updateOrCreate(
            [
                'user_id' => $mahasiswaUser->id,
                'jenis_surat' => 'Surat Rekomendasi Beasiswa'
            ],
            [
                'keperluan' => 'Pendaftaran Beasiswa Bank Indonesia 2026',
                'status' => 'pending',
                'tanggal_pengajuan' => now(),
            ]
        );

        // 6. Buat Lampiran Dokumen Surat
        Dokumen::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'nama_file' => 'transkrip_nilai_budi.pdf',
                'file_path' => 'documents/transkrip_nilai_budi.pdf',
                'format' => 'PDF',
                'ukuran' => 102450, // ~100kb
            ]
        );

        // 7. Buat Konsultasi Dummy
        Consultation::updateOrCreate(
            [
                'user_id' => $mahasiswaUser->id,
                'tujuan' => 'staff'
            ],
            [
                'jenis' => 'online',
                'jadwal' => now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0),
                'status' => 'menunggu',
                'penerima_id' => $staffUser->id,
                'keterangan' => 'Konsultasi kelengkapan berkas beasiswa.',
            ]
        );

        // 8. Buat Live Chat & Pesan Dummy
        $chat = LiveChat::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaUser->id,
                'staff_id' => $staffUser->id
            ],
            [
                'status' => 'aktif'
            ]
        );

        Message::firstOrCreate(
            [
                'chat_id' => $chat->id,
                'message' => 'Halo pak, saya ingin menanyakan mengenai verifikasi berkas pengajuan surat saya.',
            ],
            [
                'sender_id' => $mahasiswaUser->id,
                'sent_at' => now()->subMinutes(10),
            ]
        );

        Message::firstOrCreate(
            [
                'chat_id' => $chat->id,
                'message' => 'Halo Budi, berkas Anda sedang diproses oleh staf kemahasiswaan ya. Silakan ditunggu.',
            ],
            [
                'sender_id' => $staffUser->id,
                'sent_at' => now()->subMinutes(5),
            ]
        );

        // 9. Buat Notifikasi Dummy
        Notification::updateOrCreate(
            [
                'user_id' => $mahasiswaUser->id,
                'title' => 'Beasiswa Baru Tersedia'
            ],
            [
                'message' => 'Beasiswa Unggulan Eksternal Bank Indonesia baru saja dibuka. Silakan daftar!',
                'type' => 'beasiswa',
                'status' => 'unread',
                'link' => '/beasiswa',
            ]
        );

        // 10. Buat Jenis Surat Master
        \App\Models\JenisSurat::updateOrCreate(
            ['nama' => 'Surat Rekomendasi Beasiswa'],
            ['deskripsi' => 'Surat rekomendasi resmi dari pihak fakultas untuk pengajuan beasiswa.']
        );
        \App\Models\JenisSurat::updateOrCreate(
            ['nama' => 'Surat Keterangan Aktif Kuliah'],
            ['deskripsi' => 'Surat keterangan resmi yang menyatakan bahwa mahasiswa tersebut aktif kuliah pada semester berjalan.']
        );
        \App\Models\JenisSurat::updateOrCreate(
            ['nama' => 'Surat Keterangan Kelakuan Baik'],
            ['deskripsi' => 'Surat keterangan resmi yang menyatakan bahwa mahasiswa berkelakuan baik dan tidak pernah melanggar tata tertib.']
        );

        // 11. Buat Jenis Beasiswa Master
        \App\Models\JenisBeasiswa::updateOrCreate(
            ['nama' => 'Beasiswa Bank Indonesia (BI)'],
            ['deskripsi' => 'Program beasiswa dari Bank Indonesia untuk mahasiswa berprestasi.']
        );
        \App\Models\JenisBeasiswa::updateOrCreate(
            ['nama' => 'Beasiswa Prestasi Akademik PPA'],
            ['deskripsi' => 'Program beasiswa Peningkatan Prestasi Akademik dari Kementerian Riset dan Teknologi.']
        );
        \App\Models\JenisBeasiswa::updateOrCreate(
            ['nama' => 'Beasiswa Djarum Foundation'],
            ['deskripsi' => 'Beasiswa prestasi dari Djarum Foundation beserta program pelatihan soft skills.']
        );
    }
}
