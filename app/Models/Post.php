<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'inspection_id',
        'vehicle_id',
        'post_inspection_date',
        'post_model',
        'post_license_plate',
        'post_comments',
        'post_images',
        'post_kilometer',
        'post_jam_keluar',
        'post_bbm',
        'post_peralatan',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'post_images' => 'array',
        'post_peralatan' => 'array',
    ];

    public $skipHistory = false;

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public static function averagePostRating($vehicleId = null)
    {
        $query = static::whereNotNull('rating');
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }
        return $query->avg('rating');
    }

    protected static function booted()
    {
        static::saving(function ($post) {
            if ($post->post_inspection_date && !$post->skipHistory) {
                $vehicle = Vehicle::where('license_plate', $post->post_license_plate)->first();
                if ($vehicle) {
                    $vehicle->status = 'Available';
                    $vehicle->save();
                }

                $request = $post->request;
                if ($request) {
                    $request->status = 'Completed';
                    $request->save();

                    $user = $request->user;
                    if ($user) {
                        Notification::make()
                            ->title(__('filament.notification.post_title'))
                            ->body(__('filament.notification.post_body'))
                            ->sendToDatabase([$user]);
                    }

                    History::create([
                        'request_id' => $request->id,
                        'vehicle_id' => $post->vehicle_id,
                        'post_id' => $post->id,
                    ]);
                }
            }
        });
    }
}
