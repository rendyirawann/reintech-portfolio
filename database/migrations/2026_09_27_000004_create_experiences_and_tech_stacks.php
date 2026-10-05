<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat (kerja, pendidikan, organisasi) untuk timeline halaman depan,
 * CV, dan resume; daftar tech stack yang dikelola; statistik otomatis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $t) {
            $t->id();
            $t->string('type', 20)->default('work');          // work | education | organization
            $t->string('title', 160);                          // jabatan / gelar
            $t->string('organization', 160);                   // instansi / kampus
            $t->string('location', 160)->nullable();
            $t->string('employment_type', 60)->nullable();     // Penuh waktu, Kontrak, ...
            $t->date('start_date');
            $t->date('end_date')->nullable();
            $t->boolean('is_current')->default(false);
            $t->text('description')->nullable();               // satu baris = satu poin
            $t->boolean('is_visible')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('tech_stacks', function (Blueprint $t) {
            $t->id();
            $t->string('name', 60);
            $t->string('slug', 60)->nullable();                // nama ikon simple-icons
            $t->string('color', 7)->nullable();                // warna merek, #rrggbb
            $t->string('group', 40)->default('Backend');       // Backend | Frontend | Mobile | Database | Tools
            $t->boolean('is_visible')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::table('hero_stats', function (Blueprint $t) {
            // years (counter_value = tahun mulai) | projects | web | mobile
            $t->string('auto_key', 20)->nullable()->after('label');
        });

        Schema::table('portfolio_identity', function (Blueprint $t) {
            $t->text('summary')->nullable();
            $t->string('languages', 255)->nullable();          // "Indonesia (asli), Inggris (menengah)"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('tech_stacks');
        Schema::table('hero_stats', fn (Blueprint $t) => $t->dropColumn('auto_key'));
        Schema::table('portfolio_identity', fn (Blueprint $t) => $t->dropColumn(['summary', 'languages']));
    }
};
