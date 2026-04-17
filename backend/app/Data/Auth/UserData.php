<?php

declare(strict_types=1);

namespace App\Data\Auth;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserData extends Data
{
    /** @param list<string> $roles */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly array $roles,
    ) {}

    public static function fromModel(User $user): self
    {
        /** @var list<string> $roles */
        $roles = $user->getRoleNames()->values()->all();

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            roles: $roles,
        );
    }
}
