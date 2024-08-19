<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'initial_id',
        'status',
        'final_reason',
    ];

    public function initial()
    {
        return $this->belongsTo(Initial::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function inspection()
    {
        return $this->hasMany(Inspection::class);
    }

    protected static function booted()
    {
        static::saved(function ($approval) {
            if ($approval->wasChanged()) {
                if ($approval->request) {
                    $approval->request->update([
                        'status' => $approval->status,
                        'approval_reason' => $approval->final_reason,
                    ]);
                }

                if ($approval->status === 'Approved') {
                    Inspection::create([
                        'request_id' => $approval->request_id,
                        'approval_id' => $approval->id,
                    ]);
                }
            }
        });
    }
}
