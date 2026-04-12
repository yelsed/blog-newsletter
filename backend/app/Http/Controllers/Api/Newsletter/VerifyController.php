<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsletter;

use App\Actions\Newsletter\VerifySubscriberAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VerifyController extends Controller
{
    public function __invoke(string $token, VerifySubscriberAction $action): JsonResponse
    {
        $action->execute($token);

        return response()->json([
            'message' => __('newsletter.verified'),
        ]);
    }
}
