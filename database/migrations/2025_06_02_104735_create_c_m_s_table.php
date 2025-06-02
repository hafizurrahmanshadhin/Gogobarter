<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('c_m_s', function (Blueprint $table) {
            $table->id();

            $table->enum('section', ['hero', 'service', 'instruction', 'trading'])->nullable(false)->index();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->json('items')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('c_m_s');
    }
};
