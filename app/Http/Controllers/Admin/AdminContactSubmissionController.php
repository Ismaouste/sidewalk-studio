<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Inertia\Inertia;
use Inertia\Response;

class AdminContactSubmissionController extends Controller
{
    public function index(): Response
    {
        ContactSubmission::query()
            ->where('status', 'new')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

        $submissions = ContactSubmission::query()
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(fn (ContactSubmission $submission): array => [
                'id' => $submission->id,
                'locale' => $submission->locale,
                'name' => $submission->name,
                'email' => $submission->email,
                'company' => $submission->company,
                'summary' => $submission->summary,
                'status' => $submission->status,
                'created_at' => $submission->created_at?->toAtomString(),
                'read_at' => $submission->read_at?->toAtomString(),
            ]);

        return Inertia::render('Admin/ContactSubmissions/Index', [
            'submissions' => $submissions,
        ]);
    }
}
