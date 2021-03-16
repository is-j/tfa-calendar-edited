<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutor extends Model
{
    use HasFactory;

    protected $attributes = [
        'languages' => '[1]'
    ];

    protected $fillable = [
        'user_id',
        'bio',
        'subjects',
        'meeting_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return json_decode($this->subjects);
    }
}
