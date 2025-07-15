<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomNotification extends DatabaseNotification
{
    use HasFactory; 
}
