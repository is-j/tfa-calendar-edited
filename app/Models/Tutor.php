<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'subjects',
        'meeting_link'
    ];

    protected $primaryKey = 'user_id';

    public function subjects()
    {
        return json_decode($this->subjects)->subjects;
    }
}
