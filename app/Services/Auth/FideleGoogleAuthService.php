<?php

namespace App\Services\Auth;

use App\Models\Fidele;
use Illuminate\Validation\ValidationException;

class FideleGoogleAuthService
{
    public function __construct(
        private readonly GoogleIdTokenVerifier $verifier,
    ) {}

    /**
     * @return array{user: Fidele, token: string}
     */
    public function authenticate(string $idToken): array
    {
        $payload = $this->verifier->verify($idToken);

        $googleId = (string) $payload['sub'];
        $email = (string) $payload['email'];
        [$prenom, $nom] = $this->resolveNames($payload);

        $fidele = Fidele::query()->where('google_id', $googleId)->first();

        if ($fidele === null) {
            $existing = Fidele::query()->where('email', $email)->first();

            if ($existing !== null) {
                if ($existing->google_id !== null && $existing->google_id !== $googleId) {
                    throw ValidationException::withMessages([
                        'email' => ['Cet e-mail est déjà associé à un autre compte Google.'],
                    ]);
                }

                $existing->forceFill(['google_id' => $googleId])->save();
                $fidele = $existing->fresh();
            } else {
                $fidele = Fidele::query()->create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => null,
                    'pays' => 'Burkina Faso',
                    'actif' => true,
                ]);
            }
        }

        if (! $fidele->actif) {
            abort(403, 'Ce compte est désactivé.');
        }

        $fidele->tokens()->where('name', 'fidele-api')->delete();
        $token = $fidele->createToken('fidele-api')->plainTextToken;

        return [
            'user' => $fidele->fresh(),
            'token' => $token,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{0: string, 1: string}
     */
    private function resolveNames(array $payload): array
    {
        $prenom = trim((string) ($payload['given_name'] ?? ''));
        $nom = trim((string) ($payload['family_name'] ?? ''));

        if ($prenom === '' && $nom === '' && ! empty($payload['name'])) {
            $parts = preg_split('/\s+/', trim((string) $payload['name']), 2) ?: [];
            $prenom = $parts[0] ?? 'Utilisateur';
            $nom = $parts[1] ?? 'Google';
        }

        if ($prenom === '') {
            $prenom = 'Utilisateur';
        }

        if ($nom === '') {
            $nom = 'MesseConnect';
        }

        return [$prenom, $nom];
    }
}
