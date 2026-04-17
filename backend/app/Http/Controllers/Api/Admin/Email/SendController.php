<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Email;

use App\Actions\Admin\SendEmailAction;
use App\Enums\EmailStatus;
use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\JsonResponse;

class SendController extends Controller
{
    public function __invoke(Email $email, SendEmailAction $action): JsonResponse
    {
        if ($email->status !== EmailStatus::Draft) {
            return response()->json(
                ['message' => __('admin.email.already_sent')],
                status: 409,
            );
        }

        $action->onQueue()->execute($email);

        return response()->json(
            ['message' => __('admin.email.send_queued')],
            status: 202,
        );
    }
}
