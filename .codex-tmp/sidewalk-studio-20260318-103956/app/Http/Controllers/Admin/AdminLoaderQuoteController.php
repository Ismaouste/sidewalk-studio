<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\LoaderQuoteService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminLoaderQuoteController extends Controller
{
    public function __construct(
        protected LoaderQuoteService $loaderQuotes,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/LoaderQuotes/Index', [
            'quotes' => $this->loaderQuotes->adminList(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'quotes' => ['required', 'array'],
            'quotes.*.id' => ['nullable', 'integer'],
            'quotes.*.text' => ['required', 'string', 'max:500'],
            'quotes.*.type' => ['required', 'string', 'in:message,quote'],
            'quotes.*.author' => ['nullable', 'string', 'max:160'],
            'quotes.*.locale' => ['required', 'string', 'in:en,fr'],
            'quotes.*.is_active' => ['required', 'boolean'],
            'quotes.*.theme_target' => ['nullable', 'string', 'in:morning,sunset'],
            'quotes.*.weight' => ['nullable', 'integer', 'between:0,999'],
        ]);

        $this->loaderQuotes->saveMany($payload['quotes']);
        $this->siteSettings->markRebuildRequired('Loader quotes changed.');

        $this->auditLogs->recordAction(
            action: 'loader_quotes.saved',
            subject: 'loader_quotes',
            summary: [
                'quote_count' => count($payload['quotes']),
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.loader-quotes.index')
            ->with('status', 'Loader quotes saved.');
    }
}
