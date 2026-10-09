<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Catat tindakan penting. Metadata tidak boleh berisi password atau rahasia lain.
     *
     * @param  array<string, mixed>  $metadata
     */
    public static function log(string $action, ?Model $subject = null, array $metadata = [], ?int $actorId = null): AuditLog
    {
        return AuditLog::create([
            'actor_user_id' => $actorId ?? Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
