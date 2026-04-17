<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Auth\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request): UserData
    {
        /** @var User $user */
        $user = $request->user();

        return UserData::fromModel($user);
    }
}
