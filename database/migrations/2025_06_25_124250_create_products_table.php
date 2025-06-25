<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('product_category_id');
            $table->foreign('product_category_id')->references('id')->on('product_categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->string('name')->nullable(false);
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->string('condition')->nullable(false);

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
