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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('document'); 
            $table->string('email'); 
            $table->string('cellphone'); 
            $table->boolean('disabled')->default(false);
            $table->timestamps();
        });

        Schema::create('providersAddress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('cep', 9); 
            $table->string('street', 70); 
            $table->string('number', 10); 
            $table->string('city', 60); 
            $table->string('state', 60); 
            $table->string('neighborhood', 60); 
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
