<?php

namespace App\Models;

use App\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HppLog extends Model
{
    use HasFactory, UsesUlid;

    protected $table = 'hpp_logs';

    protected $fillable = [
        'hpp_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'notes',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with HPP
     */
    public function hpp()
    {
        return $this->belongsTo(Hpp::class, 'hpp_id');
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get action display name
     */
    public function getActionDisplayAttribute()
    {
        $actions = [
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'submitted' => 'Disubmit',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'commented' => 'Berkomentar',
            'reviewed' => 'Direview',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute($status)
    {
        $statuses = [
            'draft' => 'Draft',
            'submitted' => 'Disubmit',
            'under_review' => 'Sedang Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$status] ?? $status;
    }
}