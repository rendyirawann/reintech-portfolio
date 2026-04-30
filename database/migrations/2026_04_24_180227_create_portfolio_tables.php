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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('hero_data', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('title');
            $table->text('description');
            $table->json('stats')->nullable();
            $table->timestamps();
        });

        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->nullable();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('about_cards', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('content');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('price')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('about_cards');
        Schema::dropIfExists('about_sections');
        Schema::dropIfExists('hero_data');
        Schema::dropIfExists('site_settings');
    }
};
