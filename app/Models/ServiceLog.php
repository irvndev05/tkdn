<?php

namespace App\Models;

use App\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use HasFactory, UsesUlid;

    protected $fillable = [
        'service_id',
        'user_id',
        'action',
        'notes',
    ];

    /**
     * Get the service that owns the log.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
