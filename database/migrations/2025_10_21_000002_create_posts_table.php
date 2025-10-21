<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug'); // BAD: No index
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('user_id'); // BAD: Foreign key without index
            $table->unsignedBigInteger('category_id'); // BAD: Foreign key without index
            $table->boolean('is_published')->default(false); // BAD: No index, will be filtered
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable(); // BAD: No index, will be sorted
            $table->timestamps();

            // BAD: No indexes on foreign keys or frequently queried columns
            // BAD: No composite index on (is_published, published_at) for common queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
