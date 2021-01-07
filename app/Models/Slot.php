<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'start',
        'tutor_id'
    ];
}
