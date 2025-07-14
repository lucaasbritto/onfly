<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    protected $fillable = [
        'user_id', 'destino', 'data_ida', 'data_volta', 'status','updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
