<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();

            //relacion con mascota
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');

            //quien lo envia, cuidador/admin
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->text('mensaje');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
