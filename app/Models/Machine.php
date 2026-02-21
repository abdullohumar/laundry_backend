<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'status',
        'position_x',
        'position_y',
        'started_at'
    ];
    
    protected $casts = [
        'position' => 'datetime'
    ];
}
