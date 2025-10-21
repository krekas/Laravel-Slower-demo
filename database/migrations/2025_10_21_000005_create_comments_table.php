<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // BAD: Foreign key without index
            $table->unsignedBigInteger('user_id'); // BAD: Foreign key without index
            $table->unsignedBigInteger('parent_id')->nullable(); // BAD: No index for self-referencing
            $table->text('content');
            $table->boolean('is_approved')->default(false); // BAD: No index
            $table->timestamps();

            // BAD: No indexes on foreign keys
            // BAD: No index on created_at which will be used for sorting
            // BAD: No index on is_approved which will be filtered
            // BAD: No composite index on (post_id, is_approved, created_at)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
