<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('post_tag', function (Blueprint $table) {
            $table->index('post_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
