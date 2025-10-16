<?php

namespace App\Services;

use App\Models\Hpp;
use App\Models\HppLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HppApprovalService
{
    /**
     * Submit HPP for review
     */
    public function submitForReview(Hpp $hpp, $notes = null)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $hpp->status;
            
            $hpp->update([
                'status' => 'submitted',
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            // Log the submission
            $this->logAction($hpp, 'submitted', $oldStatus, 'submitted', $notes);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Approve HPP
     */
    public function approve(Hpp $hpp, $notes = null)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $hpp->status;
            
            $updateData = [
                'updated_by' => Auth::id(),
            ];
            
            // Only update status, approved_by, and approved_at if not already approved
            if ($hpp->status !== 'approved') {
                $updateData['status'] = 'approved';
                $updateData['approved_by'] = Auth::id();
                $updateData['approved_at'] = now();
            }
            
            $hpp->update($updateData);
            
            // Log the approval action to hpp_logs table
            $this->logAction($hpp, 'approved', $oldStatus, 'approved', $notes);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reject HPP
     */
    public function reject(Hpp $hpp, $notes = null)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $hpp->status;
            
            $hpp->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_notes' => $notes,
            ]);

            // Log the rejection
            $this->logAction($hpp, 'rejected', $oldStatus, 'rejected', $notes);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Add comment to HPP
     */
    public function addComment(Hpp $hpp, $comment)
    {
        DB::beginTransaction();
        try {
            // Update updated_by
            $hpp->update([
                'updated_by' => Auth::id(),
            ]);
            
            // Log the comment action to hpp_logs table
            $this->logAction($hpp, 'commented', $hpp->status, $hpp->status, $comment);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Start review process
     */
    public function startReview(Hpp $hpp, $notes = null)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $hpp->status;
            
            $hpp->update([
                'status' => 'under_review',
                'updated_by' => Auth::id(),
            ]);

            // Log the review start
            $this->logAction($hpp, 'reviewed', $oldStatus, 'under_review', $notes);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Log an action
     */
    public function logAction(Hpp $hpp, $action, $statusFrom = null, $statusTo = null, $notes = null, $changes = null)
    {
        return HppLog::create([
            'hpp_id' => $hpp->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'notes' => $notes,
            'changes' => $changes,
        ]);
    }

    /**
     * Get available actions for current user and HPP status
     */
    public function getAvailableActions(Hpp $hpp)
    {
        $actions = [];
        
        // Approve action - available if not already approved
        if ($hpp->status !== 'approved') {
            $actions[] = 'approve';
        }
        
        // Comment action - always available
        $actions[] = 'comment';

        return $actions;
    }

    /**
     * Check if HPP can be used in services
     */
    public function canBeUsedInService(Hpp $hpp)
    {
        return $hpp->status === 'approved';
    }

    /**
     * Get HPP status badge class
     */
    public function getStatusBadgeClass($status)
    {
        $classes = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        return $classes[$status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get HPP status display name
     */
    public function getStatusDisplay($status)
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