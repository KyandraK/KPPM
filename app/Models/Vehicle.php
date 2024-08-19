<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'model',
        'year',
        'vin',
        'license_plate',
        'wheels',
        'image',
        'status',
    ];

    protected $casts = [
        'image' => 'array',
    ];

    public function request()
    {
        return $this->hasMany(Request::class);
    }

    public function inspection()
    {
        return $this->hasMany(Inspection::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }
}
