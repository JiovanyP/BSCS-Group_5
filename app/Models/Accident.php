<?php
// app/Models/Accident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accident extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'location',
        'accident_type',
        'description',
        'photo_path',
        'urgency'
    ];
}