<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Initial extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'status',
        'initial_reason',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function approval()
    {
        return $this->hasMany(Approval::class);
    }

    protected static function booted()
    {
        static::saved(function ($initial) {
            if ($initial->wasChanged()) {
                if ($initial->request) {
                    $initial->request->update([
                        'status' => 'Waiting Approval by Kepala Div. Administrasi Umum',
                        'approval_reason' => $initial->initial_reason,
                    ]);
                }

                if ($initial->status === 'Approved') {
                    Approval::create([
                        'request_id' => $initial->request_id,
                        'initial_id' => $initial->id,
                    ]);
                }
            }
        });
    }
}
