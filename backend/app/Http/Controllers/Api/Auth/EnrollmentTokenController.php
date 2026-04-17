<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Data\Auth\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnrollmentTokenController extends Controller
{
    public function __invoke(Request $request, string $token): JsonResponse
    {
        $cacheKey = "passkey:enroll:{$token}";
        $userId = Cache::get($cacheKey);

        if (! is_int($userId)) {
            return response()->json(['message' => __('auth.invalid_enrollment_token')], 404);
        }

        $user = User::find($userId);
        if ($user === null) {
            Cache::forget($cacheKey);

            return response()->json(['message' => __('auth.invalid_enrollment_token')], 404);
        }

        Cache::forget($cacheKey);
        auth()->guard('web')->login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json(UserData::fromModel($user)->toArray());
    }
}
