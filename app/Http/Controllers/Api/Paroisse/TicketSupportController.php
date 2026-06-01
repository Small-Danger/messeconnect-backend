<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreTicketSupportRequest;
use App\Http\Resources\Api\Paroisse\TicketSupportResource;
use App\Models\TicketSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketSupportController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $tickets = $this->paroisse($request)
            ->ticketsSupport()
            ->with('userParoisse')
            ->latest()
            ->get();

        return response()->json([
            'tickets' => TicketSupportResource::collection($tickets),
        ]);
    }

    public function store(StoreTicketSupportRequest $request): JsonResponse
    {
        $user = $this->userParoisse($request);

        $ticket = $this->paroisse($request)->ticketsSupport()->create([
            ...$request->validated(),
            'user_paroisse_id' => $user->id,
            'statut' => 'ouvert',
        ]);

        $ticket->load('userParoisse');

        return response()->json([
            'message' => 'Ticket support créé.',
            'ticket' => new TicketSupportResource($ticket),
        ], 201);
    }

    public function show(Request $request, TicketSupport $ticketSupport): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $ticketSupport);
        $ticketSupport->load('userParoisse');

        return response()->json([
            'ticket' => new TicketSupportResource($ticketSupport),
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, TicketSupport $ticketSupport): void
    {
        abort_unless(
            $ticketSupport->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
