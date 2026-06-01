<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Fidele\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();

        $notifications = $fidele->notifications()
            ->latest('date_envoi')
            ->get();

        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }

    public function marquerCommeLue(Request $request, Notification $notification): JsonResponse
    {
        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();

        abort_unless($notification->fidele_id === $fidele->id, 404);

        $notification->update(['statut' => 'lue']);

        return response()->json([
            'message' => 'Notification marquée comme lue.',
            'notification' => new NotificationResource($notification),
        ]);
    }
}
