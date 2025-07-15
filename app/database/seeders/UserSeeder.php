<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Fulano',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123456'),
            'is_admin' => false,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@teste.com',
            'password' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        User::factory(4)->create([
            'is_admin' => false,
        ]);
    }
}
