<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 200);
            $table->string('email', length: 150)->unique();
            $table->string('role', length: 100);
            $table->string('rm', length: 50);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('disabled')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(["name" => "admin", "email" => "admin@gmail.com", "role" => "Admin", "rm" => "123456789", "password" => '$2y$10$YBau0xo9OqwFPEugzak.Nu6hBihsGfEBQvmiJTRg3IoX8LUPJs.Iq', 'disabled' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
