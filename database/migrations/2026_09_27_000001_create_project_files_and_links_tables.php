<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dua tabel anak baru untuk proyek: berkas pendukung dan tautan.
 *
 * Gambar sudah punya tabelnya sendiri (`project_images`). Berkas dipisah
 * karena yang disimpan berbeda jenisnya — nama asli, ukuran, dan tipe MIME
 * perlu ditampilkan ke pengunjung, sedangkan gambar tidak butuh itu.
 *
 * Tautan dibuat sebagai tabel, bukan kolom tambahan. Sebelumnya hanya ada
 * `live_url` dan `repo_url`; begitu sebuah proyek punya demo, repo, artikel,
 * dan tautan Play Store sekaligus, menambah kolom lagi berarti mengubah skema
 * setiap kali ada jenis tautan baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Tabel selalu dibaca "berkas milik satu proyek, terurut" —
            // index gabungan menjawabnya tanpa tahap sort tersendiri.
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('project_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('url', 500);
            // Ikon tidak disimpan sebagai SVG. Yang disimpan hanya NAMA
            // platform hasil pengenalan URL, sehingga saat ikonnya diperbarui
            // seluruh baris lama ikut berubah tanpa migrasi data.
            $table->string('platform', 40)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_links');
        Schema::dropIfExists('project_files');
    }
};
