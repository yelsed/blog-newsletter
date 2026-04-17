<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Email;

use App\Actions\Admin\RenderEmailHtmlAction;
use App\Data\Admin\Blocks\BlockDataFactory;
use App\Data\Admin\EmailData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Email\PreviewEmailRequest;
use Illuminate\Http\JsonResponse;

class PreviewController extends Controller
{
    public function __invoke(PreviewEmailRequest $request, RenderEmailHtmlAction $render): JsonResponse
    {
        /** @var array{subject: string, blocks: array<int, array<string, mixed>>} $validated */
        $validated = $request->validated();

        $data = new EmailData(
            subject: $validated['subject'],
            blocks: array_values(array_map(
                static fn (array $payload) => BlockDataFactory::fromArray($payload),
                $validated['blocks'],
            )),
        );

        return response()->json([
            'html' => $render->execute($data),
        ]);
    }
}
