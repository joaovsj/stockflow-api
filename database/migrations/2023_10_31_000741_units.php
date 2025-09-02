<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10);
            // $table->float('unity', 8, 3);
            $table->timestamps();
        }); 

        DB::table('units')->insert(['id' => 1, 'name' => 'Pacote', 'created_at' => date('Y-m-d h:i:s')]);
        DB::table('units')->insert(['id' => 2, 'name' => 'Kilograma', 'created_at' => date('Y-m-d h:i:s')]);
        DB::table('units')->insert(['id' => 3, 'name' => 'Litro', 'created_at' => date('Y-m-d h:i:s')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
