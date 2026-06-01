<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use App\Models\JournalAudit;
use App\Models\User;
use Illuminate\Http\Request;

trait LogsAdminAction
{
    /**
     * @param  array<string, mixed>  $details
     */
    protected function logAdminAction(Request $request, string $action, array $details = []): void
    {
        /** @var User $admin */
        $admin = $request->user();

        JournalAudit::query()->create([
            'acteur_type' => User::class,
            'acteur_id' => $admin->id,
            'action' => $action,
            'details' => $details === [] ? null : json_encode($details, JSON_UNESCAPED_UNICODE),
            'ip_address' => $request->ip(),
        ]);
    }
}
