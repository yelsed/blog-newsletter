<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Signature('admin:enroll-passkey {email}')]
#[Description('Issue a one-time URL that lets the admin enroll a passkey without a prior login.')]
class AdminEnrollPasskey extends Command
{
    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $user = User::where('email', $email)->first();
        if ($user === null) {
            $this->error("No user found with email {$email}");

            return self::FAILURE;
        }

        if (! $user->hasRole('admin')) {
            $this->error("User {$email} is not an admin.");

            return self::FAILURE;
        }

        $token = Str::random(48);
        Cache::put("passkey:enroll:{$token}", $user->id, now()->addMinutes(10));

        $url = rtrim((string) config('app.frontend_url'), '/')."/admin/register?token={$token}";

        $this->info('Passkey enrollment URL (valid for 10 minutes):');
        $this->line($url);

        return self::SUCCESS;
    }
}
