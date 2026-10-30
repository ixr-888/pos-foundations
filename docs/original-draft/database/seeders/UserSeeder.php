<?php

namespace Database\Seeders;

use App\Http\Controllers\UserController;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserController::sampleRecords() as $user) {
            $existing = DB::table('users')->where('id', $user['id'])->first();

            DB::table('users')->updateOrInsert(
                ['id' => $user['id']],
                array_merge($user, [
                    // This listing exercise does not expose a login screen.
                    // Preserve an existing hash when this seeder is run again.
                    'password' => $existing?->password ?? Hash::make(Str::random(40)),
                    'created_at' => $existing?->created_at ?? now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
