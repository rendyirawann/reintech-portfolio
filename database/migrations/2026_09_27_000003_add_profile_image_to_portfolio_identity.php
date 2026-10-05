<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto profil pemilik portofolio.
 *
 * Terpisah dari `og_image`: yang satu foto orang untuk bagian Tentang dan
 * dokumen ekspor, yang satu gambar pratinjau saat tautan dibagikan — ukuran,
 * rasio, dan isinya berbeda, dan memaksakan satu kolom untuk keduanya berarti
 * salah satunya selalu salah bentuk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_identity', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('og_image');
            $table->string('full_name')->nullable()->after('profile_image');
            $table->string('headline')->nullable()->after('full_name');
            $table->string('location')->nullable()->after('headline');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_identity', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'full_name', 'headline', 'location']);
        });
    }
};
