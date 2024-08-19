<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'wheels',
        'departure_time',
        'return_time',
        'reason',
        'status',
        'approval_reason',
        'rating',
        'feedback',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function intials()
    {
        return $this->hasMany(Initial::class);
    }

    public function approval()
    {
        return $this->hasMany(Approval::class);
    }

    public function inspection()
    {
        return $this->hasMany(Inspection::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    protected static function booted()
    {
        static::created(function ($request) {
            Initial::create([
                'request_id' => $request->id,
            ]);
        });
    }

    public static function averageRequestRating($vehicleId = null)
    {
        $query = static::whereNotNull('rating');
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }
        return $query->avg('rating');
    }
}
