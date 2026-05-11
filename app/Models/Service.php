<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    // This tells Laravel which columns are safe to save to
    protected $fillable = [
    'business_name', 
    'description', 
    'contact_number', 
    'address', 
    'services_offered', 
    'logo_url',
    'user_id', // NEW
    'status'   // NEW
];

// Add this relationship so you can get the user who owns the service
public function user() {
    return $this->belongsTo(User::class);
}
}