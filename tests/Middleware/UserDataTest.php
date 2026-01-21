<?php

namespace MATests\Middleware;

use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;
use MATests\TestCase;

class UserDataTest extends TestCase
{
    public function test_user_has_correct_keys()
    {
        $this->get('get-data', $this->headers())->assertSuccessful();

        $user = auth()->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertInstanceOf(Role::class, $user->role);
    }
}