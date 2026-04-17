<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmailStatus;
use Carbon\CarbonImmutable;
use Database\Factories\EmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $subject
 * @property array<int, array<string, mixed>> $blocks
 * @property EmailStatus $status
 * @property CarbonImmutable|null $sent_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read User|null $author
 */
#[Fillable(['user_id', 'subject', 'blocks', 'status', 'sent_at'])]
class Email extends Model
{
    /** @use HasFactory<EmailFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'status' => EmailStatus::class,
            'sent_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @param Builder<self> $query */
    public function scopeDrafts(Builder $query): void
    {
        $query->where('status', EmailStatus::Draft);
    }

    /** @param Builder<self> $query */
    public function scopeSent(Builder $query): void
    {
        $query->where('status', EmailStatus::Sent);
    }
}
