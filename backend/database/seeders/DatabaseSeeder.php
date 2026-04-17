<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(PermissionsSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => config('newsletter.admin.email')],
            [
                'name' => config('newsletter.admin.name'),
                'password' => Str::random(40),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}
