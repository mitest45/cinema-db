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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_danish');
            $table->string('slug')->nullable()->unique();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->date('release_date')->nullable();
            $table->string('duration')->nullable();
            $table->string('language')->nullable();
            $table->string('poster')->nullable();
            $table->string('trailer')->nullable();
            $table->string('tmdb_id')->nullable()->index();
            $table->string('imdb_id')->nullable()->index();
            $table->string('dfi_id')->nullable()->index();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
