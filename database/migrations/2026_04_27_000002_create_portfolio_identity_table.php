<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_identity', function (Blueprint $table) {
            $table->id();
            $table->string('logo_text', 10)->default('RD');
            $table->string('logo_subtext', 50)->default('reintech.dev');
            $table->string('topbar_status_text', 100)->default('Available for projects');
            $table->string('topbar_role_text', 100)->default('Full Stack Dev');
            $table->string('sidebar_icon_type', 10)->default('text'); // 'text' | 'image'
            $table->string('sidebar_icon_value', 255)->default('RD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_identity');
    }
};
