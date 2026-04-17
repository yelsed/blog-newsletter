<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

class RegisteredPasskeyController extends Controller
{
    public function create(AttestationRequest $request): Responsable
    {
        return $request->fastRegistration()->toCreate();
    }

    public function store(AttestedRequest $request): Response
    {
        $request->save();

        return response()->noContent();
    }
}
