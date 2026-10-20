<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ecommerce.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'phone' => '+50370000000',
                'address' => 'San Salvador, El Salvador',
            ]
        );

        User::firstOrCreate(
            ['email' => 'cliente@ecommerce.test'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('Cliente123!'),
                'role' => 'customer',
                'phone' => '+50371111111',
                'address' => 'Santa Tecla, El Salvador',
            ]
        );

       if (class_exists(\Faker\Factory::class)) {
    User::factory()->count(8)->create();
}
    }
}
