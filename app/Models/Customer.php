<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'full_name', 'email', 'phone', 'city', 'status', 'joined_on',
    ];

    protected function casts(): array
    {
        return ['joined_on' => 'date'];
    }
}
