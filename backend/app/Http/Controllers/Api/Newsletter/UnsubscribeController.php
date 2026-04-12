<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsletter;

use App\Actions\Newsletter\UnsubscribeAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UnsubscribeController extends Controller
{
    public function __invoke(string $token, UnsubscribeAction $action): JsonResponse
    {
        $action->execute($token);

        return response()->json([
            'message' => __('newsletter.unsubscribed'),
        ]);
    }
}
