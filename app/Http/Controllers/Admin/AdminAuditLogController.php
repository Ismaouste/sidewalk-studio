<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuditLogController extends Controller
{
    public function index(): Response
    {
        $entries = AdminAuditLog::query()
            ->latest('created_at')
            ->limit(25)
            ->get()
            ->map(fn (AdminAuditLog $entry): array => [
                'id' => $entry->id,
                'actor' => [
                    'type' => $entry->actor_type,
                    'name' => $entry->actor_name,
                    'email' => $entry->actor_email,
                ],
                'action' => $entry->action,
                'subject' => $entry->subject,
                'summary' => $entry->summary ?? [],
                'created_at' => $entry->created_at?->toAtomString(),
            ]);

        return Inertia::render('Admin/AuditLog/Index', [
            'entries' => $entries,
        ]);
    }
}
