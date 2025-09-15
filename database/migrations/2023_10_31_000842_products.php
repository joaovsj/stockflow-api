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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->float('quantity', 8, 3);
            $table->float('price', 8,2);

            // $table->string('description', 255);
            
            $table->integer('minimum');
            $table->integer('maximum');

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('unity_id')->nullable();

            $table->boolean('disabled')->default(false);

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            
            $table->foreign('provider_id')
                ->references('id')
                ->on('providers')
                ->onDelete('cascade');

            $table->foreign('unity_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
