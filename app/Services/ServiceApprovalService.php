<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceApprovalService
{
    /**
     * Approve Service
     */
    public function approve(Service $service, $notes = null)
    {
        DB::beginTransaction();
        try {
            // Refresh model to get latest data
            $service->refresh();
            
            $updateData = [
                'updated_by' => Auth::id(),
            ];
            
            // Only update status, approved_by, and approved_at if not already approved
            if ($service->status !== 'approved') {
                $updateData['status'] = 'approved';
                $updateData['approved_by'] = Auth::id();
                $updateData['approved_at'] = now();
            }
            
            $service->update($updateData);
            
            // Log the approval action
            $this->logAction($service, 'approved', $notes);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to approve', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add comment to Service
     */
    public function addComment(Service $service, $comment)
    {
        DB::beginTransaction();
        try {
            // Refresh model to get latest data
            $service->refresh();
            
            // Update updated_by
            $service->update([
                'updated_by' => Auth::id(),
            ]);
            
            // Log the comment action
            $this->logAction($service, 'commented', $comment);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to add comment', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get available actions for current user and Service status
     */
    public function getAvailableActions(Service $service)
    {
        $actions = [];
        
        // Approve action - available if not already approved
        if ($service->status !== 'approved') {
            $actions[] = 'approve';
        }
        
        // Comment action - always available
        $actions[] = 'comment';
        
        return $actions;
    }

    /**
     * Check if Service can be used
     */
    public function canBeUsed(Service $service): bool
    {
        return $service->status === 'approved';
    }

    /**
     * Log an action to service_logs table
     */
    private function logAction(Service $service, string $action, ?string $notes = null)
    {
        ServiceLog::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'notes' => $notes,
        ]);
    }
}
