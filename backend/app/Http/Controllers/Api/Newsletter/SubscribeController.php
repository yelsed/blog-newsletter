<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsletter;

use App\Actions\Newsletter\SubscribeAction;
use App\Data\Newsletter\SubscribeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeRequest;
use Illuminate\Http\JsonResponse;

class SubscribeController extends Controller
{
    public function __invoke(SubscribeRequest $request, SubscribeAction $action): JsonResponse
    {
        $data = SubscribeData::from($request->validated());

        $action->execute($data);

        return response()->json([
            'message' => __('newsletter.subscribed'),
        ], 201);
    }
}
