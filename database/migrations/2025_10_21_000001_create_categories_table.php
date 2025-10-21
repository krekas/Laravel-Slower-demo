<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug'); // BAD: No index on slug, will be queried frequently
            $table->text('description')->nullable();
            $table->timestamps();
            // BAD: Missing index on slug which will be used in WHERE clauses
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
