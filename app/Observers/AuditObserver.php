<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'created', $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = [];
        foreach ($model->getChanges() as $k => $v) {
            $changes[$k] = ['from' => $model->getOriginal($k), 'to' => $v];
        }
        if (! empty($changes)) {
            $this->log($model, 'updated', $changes);
        }
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted', $model->getAttributes());
    }

    protected function log(Model $model, string $action, array $changes): void
    {
        try {
            AuditLog::create([
                'user_id' => optional(auth()->user())->id,
                'action' => $action,
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'changes' => $changes,
                'ip_address' => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // best effort
        }
    }
}
