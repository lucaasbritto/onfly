<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TravelRequest;

class TravelRequestSeeder extends Seeder
{
    public function run()
    {

        $userPadrao = User::where('email', 'teste@teste.com')->first();        
        
        TravelRequest::factory(3)->create([
            'user_id' => $userPadrao->id,
            'status' => 'solicitado',
        ]);

        
        $usuarios = User::where('is_admin', false)->where('email', '!=', 'teste@teste.com')->get();

        foreach ($usuarios as $user) {
            TravelRequest::factory(rand(1, 1))->create([
                'user_id' => $user->id,
                'status' => 'solicitado',
            ]);
        }
    }
}
