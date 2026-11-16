<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(6));

        return [
            'name' => 'Test User',
            'username' => 'user_'.$suffix,
            'email' => $suffix.'@example.test',
            'email_verified_at' => now(),
            'role' => 'Staff',
            'status' => 'Active',
            'password' => Hash::make('test-password'),
            'remember_token' => Str::random(10),
        ];
    }
}
