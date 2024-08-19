<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'vehicle_id',
        'post_id',
    ];

    protected $dates = ['created_at'];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
