<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // BAD: No index
            $table->unsignedBigInteger('tag_id'); // BAD: No index
            $table->timestamps();

            // BAD: No composite index on (post_id, tag_id)
            // BAD: No index on individual columns
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};
