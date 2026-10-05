<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Sidik jari setiap PDF yang diterbitkan, untuk halaman /verifikasi. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_segel', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 12)->unique();       // tercetak di dokumen
            $t->string('jenis', 30);
            $t->char('sha256', 64)->index();        // sidik jari berkas PDF final (sudah disegel)
            $t->unsignedInteger('ukuran');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_segel');
    }
};
