<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Email;

use App\Actions\Admin\SendTestEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Email\SendTestEmailRequest;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SendTestController extends Controller
{
    public function __invoke(SendTestEmailRequest $request, Email $email, SendTestEmailAction $action): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $targetEmail = $request->input('email') ?? $user->email;

        $action->onQueue()->execute($email, (string) $targetEmail);

        return response()->json(
            ['message' => __('admin.email.send_test_queued')],
            status: 202,
        );
    }
}
