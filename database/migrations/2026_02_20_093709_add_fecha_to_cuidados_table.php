<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('cuidados', function (Blueprint $table) {
            //fecha del cuidado
            $table->date('fecha')->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('cuidados', function (Blueprint $table) {
            $table->dropColumn('fecha');
        });
    }
};
