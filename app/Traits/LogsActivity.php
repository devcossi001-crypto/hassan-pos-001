<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            self::logActivity($model, 'created');
        });

        static::updated(function (Model $model) {
            // Only log if something actually changed
            if ($model->wasChanged()) {
                self::logActivity($model, 'updated');
            }
        });

        static::deleted(function (Model $model) {
            self::logActivity($model, 'deleted');
        });
    }

    protected static function logActivity(Model $model, string $action)
    {
        $changes = null;

        if ($action === 'updated') {
            $changes = [
                'before' => array_intersect_key($model->getOriginal(), $model->getChanges()),
                'after' => $model->getChanges(),
            ];
            
            // Mask password if it's being logged (though hopefully not)
            if (isset($changes['before']['password'])) $changes['before']['password'] = '******';
            if (isset($changes['after']['password'])) $changes['after']['password'] = '******';
        } elseif ($action === 'created') {
            $changes = ['attributes' => $model->getAttributes()];
            if (isset($changes['attributes']['password'])) $changes['attributes']['password'] = '******';
        } elseif ($action === 'deleted') {
            $changes = ['attributes' => $model->getOriginal()];
            if (isset($changes['attributes']['password'])) $changes['attributes']['password'] = '******';
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'description' => self::getActivityDescription($model, $action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected static function getActivityDescription(Model $model, string $action): string
    {
        $name = class_basename($model);
        $identifier = $model->name ?? $model->receipt_number ?? $model->id;
        
        return "{$name} '{$identifier}' was {$action}.";
    }
}
