<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data Relasional',
                'deskripsi' => 'Mata kuliah pencarian basis data relasional, normalisasi, DTD, dan optimalisasi query SQL.',
                'sks' => 3,
                'semester' => 'Genap 2025/2026', 
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Analitik Lanjutan Awan',
                'deskripsi' => 'Pembelajaran mengenai arsitektur cloud computing dan analitik data skala besar.',
                'sks' => 3,
                'semester' => 'Ganjil 2025/2026', 
                'dosen_id' => $dosen->id,
            ]
        );

        $course1->students()->syncWithoutDetaching([$mahasiswa2->id => ['status' => 'approved']]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved']
        ]);

        $tugas1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Implementasi Middleware & Eloquent ORM'],
            [
                'deskripsi' => 'Jelaskan cara kerja Route Middleware untuk proteksi peran pengguna serta implementasi relasi Eloquent pada studi kasus CeLOE LMS.',
                'deadline' => now()->addDays(7), // Deadline 7 hari lagi
            ]
        );

        $tugas2 = Assignment::updateOrCreate(
            ['course_id' => $course2->id, 'judul' => 'Tugas 1: Normalisasi Database'],
            [
                'deskripsi' => 'Lakukan normalisasi hingga 3NF pada studi kasus sistem perpustakaan yang diberikan.',
                'deadline' => now()->subDays(2), // Deadline sudah lewat (untuk uji coba)
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Middleware di Laravel berfungsi menyaring HTTP request. Misalnya middleware "role:admin" mengecek apakah user yang login memiliki role admin. Jika tidak, akses ditolak (403). Eloquent ORM digunakan untuk menghubungkan tabel, misalnya hasMany dan belongsTo antara User dan Submission.',
                'submitted_at' => now()->subDays(1),
                'nilai' => 85,
                'catatan_dosen' => 'Penjelasan sangat baik dan aplikatif. Pertahankan!',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Middleware adalah lapisan penengah antara request dan controller. Eloquent mempermudah query database dengan sintaks berorientasi objek.',
                'submitted_at' => now(),
                'nilai' => null,
                'catatan_dosen' => null,
            ]
        );

    }
}
