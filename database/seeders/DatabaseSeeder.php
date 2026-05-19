<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; //importar hash (para encriptar la contraseña)

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //crear el administrador
        //tiene rol: admin y contraseña: admin123
        User::create([
            'name' => 'administrador',
            'email' => 'residenciahuellasfelices@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
    }
}
