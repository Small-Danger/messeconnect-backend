<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateTicketSupportStatutRequest;
use App\Http\Resources\Api\Admin\TicketSupportResource;
use App\Models\TicketSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketSupportController extends Controller
{
    use LogsAdminAction;

    public function index(Request $request): JsonResponse
    {
        $query = TicketSupport::query()
            ->with(['paroisse', 'userParoisse'])
            ->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        $tickets = $query->get();

        return response()->json([
            'tickets' => TicketSupportResource::collection($tickets),
        ]);
    }

    public function show(TicketSupport $ticketSupport): JsonResponse
    {
        $ticketSupport->load(['paroisse', 'userParoisse']);

        return response()->json([
            'ticket' => new TicketSupportResource($ticketSupport),
        ]);
    }

    public function updateStatut(UpdateTicketSupportStatutRequest $request, TicketSupport $ticketSupport): JsonResponse
    {
        $statutPrecedent = $ticketSupport->statut;
        $payload = ['statut' => $request->validated('statut')];

        if ($request->filled('reponse')) {
            $payload['reponse_admin'] = $request->validated('reponse');
            $payload['reponse_admin_at'] = now();
        }

        $ticketSupport->update($payload);

        $this->logAdminAction($request, 'ticket.statut', [
            'ticket_id' => $ticketSupport->id,
            'statut_precedent' => $statutPrecedent,
            'statut' => $ticketSupport->statut,
            'reponse' => $request->filled('reponse'),
        ]);

        return response()->json([
            'message' => 'Statut du ticket mis à jour.',
            'ticket' => new TicketSupportResource($ticketSupport->fresh()->load(['paroisse', 'userParoisse'])),
        ]);
    }
}
