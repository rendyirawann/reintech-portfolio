<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pesan dari formulir kontak, dan kolom SEO yang bisa disunting dari admin.
 *
 * Pesan DISIMPAN, bukan hanya dikirim sebagai surel. Surel bisa gagal —
 * kuota SMTP habis, kredensial kedaluwarsa, penerima menandainya spam — dan
 * kalau itu satu-satunya jalur, calon klien hilang tanpa jejak. Baris di
 * tabel ini tetap ada walau pengirimannya gagal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('mail_sent_at')->nullable();
            $table->string('mail_error')->nullable();
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
        });

        Schema::table('portfolio_identity', function (Blueprint $table) {
            // Judul dan deskripsi dipisah dari logo_text: yang tampil di tab
            // peramban dan hasil pencarian sering perlu lebih panjang dan
            // lebih deskriptif daripada logo di sudut layar.
            $table->string('meta_title')->nullable()->after('logo_subtext');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('meta_keywords');
            $table->string('contact_linkedin', 500)->nullable()->after('contact_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_identity', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'og_image', 'contact_linkedin']);
        });
        Schema::dropIfExists('contact_messages');
    }
};
