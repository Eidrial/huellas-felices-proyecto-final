<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    //para añadir sin disponibilidad (en el caso de que la residencia esté llena)
    public function up(): void
    {
        DB::statement("
        ALTER TABLE estancias 
        MODIFY estado ENUM(
            'pendiente',
            'confirmada',
            'activa',
            'finalizada',
            'cancelada',
            'sin_disponibilidad'
        ) DEFAULT 'pendiente'
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE estancias 
        MODIFY estado ENUM(
            'pendiente',
            'confirmada',
            'activa',
            'finalizada',
            'cancelada'
        ) DEFAULT 'pendiente'
    ");
    }
};
