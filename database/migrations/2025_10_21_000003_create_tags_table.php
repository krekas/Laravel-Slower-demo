<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // BAD: No unique constraint or index
            $table->string('slug'); // BAD: No index
            $table->timestamps();

            // BAD: Missing index on name and slug
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
