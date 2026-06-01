<?php

namespace App\Http\Controllers\Api\Paroisse\Concerns;

use App\Models\Paroisse;
use App\Models\UserParoisse;
use Illuminate\Http\Request;

trait ResolvesParoisse
{
    protected function userParoisse(Request $request): UserParoisse
    {
        /** @var UserParoisse $user */
        $user = $request->user();

        return $user;
    }

    protected function paroisse(Request $request): Paroisse
    {
        return $this->userParoisse($request)->paroisse;
    }
}
