<?php

namespace Tests\Middleware;

use Illuminate\Support\Facades\Auth;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;
use Tests\TestCase;

class UserDataTest extends TestCase
{
    public function test_user_has_correct_keys()
    {
        $attributes = User::attributes();
        Auth::login(app(User::class, compact('attributes')));

        $user = auth()->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertInstanceOf(Role::class, $user->role);
    }
}