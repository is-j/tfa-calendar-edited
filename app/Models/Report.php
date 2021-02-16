<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = [
        'reporter_id',
        'reported_id',
        'event_id',
        'event_date'
    ];
}
