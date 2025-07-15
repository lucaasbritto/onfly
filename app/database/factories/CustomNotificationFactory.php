<?php

namespace Database\Factories;

use App\Models\CustomNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomNotificationFactory extends Factory
{
    protected $model = CustomNotification::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\TravelRequestStatusUpdated',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1, 
            'data' => ['message' => 'Notificação de teste'],
            'read_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
