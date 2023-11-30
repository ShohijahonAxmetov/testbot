<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'is_active'
    ];
}
