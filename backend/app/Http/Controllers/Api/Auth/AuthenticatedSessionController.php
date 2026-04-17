<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

class AuthenticatedSessionController extends Controller
{
    public function create(AssertionRequest $request): Responsable
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validate(['email' => 'sometimes|email|string']);

        return $request->toVerify($validated);
    }

    public function store(AssertedRequest $request): Response
    {
        return response()->noContent($request->login() ? 204 : 422);
    }

    public function destroy(Request $request): JsonResponse
    {
        auth()->guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => __('auth.logged_out')]);
    }
}
