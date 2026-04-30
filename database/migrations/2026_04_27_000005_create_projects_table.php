<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('long_description')->nullable();
            $table->string('main_image', 500)->nullable();
            $table->string('category', 100)->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('live_url', 500)->nullable();
            $table->string('repo_url', 500)->nullable();
            $table->json('tech_stack')->default('[]');
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
