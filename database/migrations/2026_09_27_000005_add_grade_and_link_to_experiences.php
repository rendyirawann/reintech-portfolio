<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $t) {
            $t->string('grade', 40)->nullable()->after('employment_type');   // IPK / nilai
            $t->string('link_label', 200)->nullable()->after('description'); // mis. judul jurnal
            $t->string('link_url', 500)->nullable()->after('link_label');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', fn (Blueprint $t) => $t->dropColumn(['grade', 'link_label', 'link_url']));
    }
};
