<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'approval_id',
        'vehicle_id',
        'inspection_date',
        'model',
        'license_plate',
        'comments',
        'images',
        'kilometer',
        'jam_keluar',
        'bbm',
        'peralatan',
    ];

    protected $casts = [
        'images' => 'array',
        'peralatan' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    protected static function booted()
    {
        static::saving(function ($inspection) {
            if ($inspection->inspection_date) {
                $vehicle = Vehicle::where('license_plate', $inspection->license_plate)->first();
                if ($vehicle) {
                    $vehicle->status = 'Ready for Pickup';
                    $vehicle->save();
                }

                $request = $inspection->request;
                if ($request) {
                    $request->status = 'Ready for Pickup';
                    $request->save();

                    $user = $request->user;
                    if ($user) {
                        $notificationBody = __('filament.notification.inspection_body_a') . ' ' . ($vehicle->model ?? 'Unknown') . ' ' . __('filament.notification.inspection_body_b') . ' ' . ($inspection->license_plate ?? 'Unknown');

                        Notification::make()
                            ->title(__('filament.notification.inspection_title'))
                            ->body($notificationBody)
                            ->sendToDatabase([$user]);
                    }
                }
            }
        });
    }
}
