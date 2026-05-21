<?php

namespace App\Observers;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class AuditTrailObserver
{
    /**
     * Handle the "created" event.
     */
    public function created($model)
    {
        $this->logAction($model, 'created');
    }

    /**
     * Handle the "updated" event.
     */
    public function updated($model)
    {
        $this->logAction($model, 'updated', [
            'before' => $model->getOriginal(),
            'after'  => $model->getChanges(),
        ]);
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted($model)
    {
        $this->logAction($model, 'deleted');
    }

    /**
     * Log action to audit_trails table.
     */
    protected function logAction($model, string $action, array $extraDetails = [])
    {
        $user = Auth::user();

        $details = array_merge([
            'model'      => get_class($model),
            'model_name' => class_basename($model),
            'id'         => $model->id,
            'attributes' => $model->getAttributes(),
        ], $extraDetails);

        AuditTrail::create([
            'user_id'    => $user?->id, // nullable if no user
            'action'     => "{$details['model_name']} {$action}",
            'details'    => json_encode($details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'ip_address' => request()->ip(),
        ]);
    }
}
