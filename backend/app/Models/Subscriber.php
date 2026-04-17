<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @use HasFactory<SubscriberFactory>
 *
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property CarbonImmutable|null $email_verified_at
 * @property string $verification_token
 * @property CarbonImmutable|null $subscribed_at
 * @property CarbonImmutable|null $unsubscribed_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
#[Fillable(['email', 'name', 'email_verified_at', 'unsubscribed_at'])]
#[Hidden(['verification_token'])]
class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'immutable_datetime',
            'subscribed_at' => 'immutable_datetime',
            'unsubscribed_at' => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber): void {
            $subscriber->verification_token = Str::random(64);
            $subscriber->subscribed_at = CarbonImmutable::now();
        });
    }

    /** @param Builder<self> $query */
    public function scopeVerified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    /** @param Builder<self> $query */
    public function scopeActive(Builder $query): void
    {
        $query->whereNotNull('email_verified_at')
            ->whereNull('unsubscribed_at');
    }
}
