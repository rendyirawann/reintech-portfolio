<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 50)->unique(); // hero, about, projects, services, contact
            $table->string('title', 255)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_sections');
    }
};
