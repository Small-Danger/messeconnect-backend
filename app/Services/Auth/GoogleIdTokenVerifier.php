<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class GoogleIdTokenVerifier
{
    /**
     * @return array<string, mixed>
     */
    public function verify(string $idToken): array
    {
        $clientId = config('services.google.client_id');

        if (empty($clientId)) {
            throw ValidationException::withMessages([
                'id_token' => ['La connexion Google n\'est pas configurée.'],
            ]);
        }

        $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'id_token' => ['Token Google invalide ou expiré.'],
            ]);
        }

        /** @var array<string, mixed> $payload */
        $payload = $response->json();

        if (($payload['aud'] ?? null) !== $clientId) {
            throw ValidationException::withMessages([
                'id_token' => ['Token Google non autorisé pour cette application.'],
            ]);
        }

        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $emailVerified || empty($payload['email'])) {
            throw ValidationException::withMessages([
                'id_token' => ['Adresse e-mail Google non vérifiée.'],
            ]);
        }

        if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
            throw ValidationException::withMessages([
                'id_token' => ['Token Google expiré.'],
            ]);
        }

        return $payload;
    }
}
