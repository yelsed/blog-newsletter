<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Admin\SaveEmailAction;
use App\Data\Admin\Blocks\BlockDataFactory;
use App\Data\Admin\EmailData;
use App\Enums\EmailStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Email\StoreEmailRequest;
use App\Http\Requests\Admin\Email\UpdateEmailRequest;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\LaravelData\PaginatedDataCollection;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    /** @return PaginatedDataCollection<int, EmailData> */
    public function index(Request $request): PaginatedDataCollection
    {
        $query = Email::query()->latest();

        $status = EmailStatus::tryFrom((string) $request->query('status', ''));
        if ($status !== null) {
            $query->where('status', $status);
        }

        return EmailData::collect(
            $query->paginate(20),
            PaginatedDataCollection::class,
        );
    }

    public function store(StoreEmailRequest $request, SaveEmailAction $action): Response
    {
        /** @var User $author */
        $author = $request->user();

        $email = $action->execute($author, $this->dataFromRequest($request));

        return EmailData::fromModel($email)
            ->toResponse($request)
            ->setStatusCode(201);
    }

    public function show(Email $email): EmailData
    {
        return EmailData::fromModel($email);
    }

    public function update(UpdateEmailRequest $request, Email $email, SaveEmailAction $action): EmailData
    {
        /** @var User $author */
        $author = $request->user();

        $email = $action->execute($author, $this->dataFromRequest($request), $email);

        return EmailData::fromModel($email);
    }

    public function destroy(Email $email): JsonResponse
    {
        $email->delete();

        return response()->json(status: 204);
    }

    private function dataFromRequest(StoreEmailRequest|UpdateEmailRequest $request): EmailData
    {
        /** @var array{subject: string, blocks: array<int, array<string, mixed>>} $validated */
        $validated = $request->validated();

        return new EmailData(
            subject: $validated['subject'],
            blocks: array_values(array_map(
                static fn (array $payload) => BlockDataFactory::fromArray($payload),
                $validated['blocks'],
            )),
        );
    }
}
