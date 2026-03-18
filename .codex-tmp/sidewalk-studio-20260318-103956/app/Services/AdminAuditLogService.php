<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AdminAuditLogService
{
    public function recordSettingsUpdated(?User $actor, array $before, array $after): ?AdminAuditLog
    {
        $summary = $this->summarizeSettingsChanges($before, $after);

        if ($summary === null) {
            return null;
        }

        return $this->record(
            actorType: $actor instanceof User ? 'user' : 'unknown',
            actor: $actor,
            action: 'site_settings.updated',
            subject: 'site_settings',
            summary: $summary,
        );
    }

    public function recordOperatorCreated(User $createdUser): AdminAuditLog
    {
        return $this->record(
            actorType: 'console',
            actor: null,
            action: 'operator.created',
            subject: 'operator_account',
            summary: [
                'created_user_email' => $createdUser->email,
                'created_user_name' => $createdUser->name,
            ],
        );
    }

    public function recordAction(
        string $action,
        string $subject,
        array $summary,
        ?User $actor = null,
        string $actorType = 'user',
    ): AdminAuditLog {
        return $this->record(
            actorType: $actor instanceof User ? $actorType : 'unknown',
            actor: $actor,
            action: $action,
            subject: $subject,
            summary: $summary,
        );
    }

    protected function record(
        string $actorType,
        ?User $actor,
        string $action,
        string $subject,
        array $summary,
    ): AdminAuditLog {
        return AdminAuditLog::query()->create([
            'actor_type' => $actorType,
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_email' => $actor?->email,
            'action' => $action,
            'subject' => $subject,
            'summary' => $summary,
        ]);
    }

    protected function summarizeSettingsChanges(array $before, array $after): ?array
    {
        $beforeFlat = Arr::dot($before);
        $afterFlat = Arr::dot($after);
        $candidateFields = collect(array_merge(array_keys($beforeFlat), array_keys($afterFlat)))
            ->unique()
            ->sort()
            ->values();

        $changedFields = $candidateFields
            ->filter(fn (string $field): bool => ($beforeFlat[$field] ?? null) !== ($afterFlat[$field] ?? null))
            ->values()
            ->all();

        if ($changedFields === []) {
            return null;
        }

        return [
            'changed_groups' => collect($changedFields)
                ->map(fn (string $field): string => Str::before($field, '.'))
                ->unique()
                ->values()
                ->all(),
            'changed_fields' => $changedFields,
            'field_count' => count($changedFields),
        ];
    }
}
