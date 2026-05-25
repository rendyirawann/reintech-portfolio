<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('portfolio_identity', function (Blueprint $table) {
            $table->string('contact_email', 100)->default('hello@reintech.dev');
            $table->string('contact_whatsapp', 50)->default('6281234567890');
            $table->string('footer_text', 255)->default('Built with ❤️ & Laravel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_identity', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_whatsapp', 'footer_text']);
        });
    }
};
