<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

trait AuthenticatesWithSanctum
{
    /**
     * @param  class-string<Model>  $modelClass
     * @return array{user: Model, token: string}
     */
    protected function attemptLogin(
        string $modelClass,
        string $email,
        string $password,
        string $tokenName,
        bool $trackLastLogin = false,
    ): array {
        /** @var Model|null $user */
        $user = $modelClass::query()->where('email', $email)->first();

        if ($user === null || empty($user->password) || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        if (! $user->actif) {
            abort(403, 'Ce compte est désactivé.');
        }

        $user->tokens()->where('name', $tokenName)->delete();

        $token = $user->createToken($tokenName)->plainTextToken;

        if ($trackLastLogin) {
            $user->forceFill(['last_login' => now()])->save();
        }

        return [
            'user' => $user->fresh(),
            'token' => $token,
        ];
    }
}
