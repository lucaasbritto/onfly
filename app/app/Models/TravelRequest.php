<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    protected $fillable = [
        'user_id', 'solicitante', 'destino', 'data_ida', 'data_volta', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
