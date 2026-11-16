<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SampleAccounts::users() as $user) {
            $existing = DB::table('users')->where('id', $user['id'])->first();

            DB::table('users')->updateOrInsert(
                ['id' => $user['id']],
                array_merge($user, [
                    // Sample accounts have no shared password; register your own account.
                    // Preserve an existing hash when this seeder is run again.
                    'password' => $existing?->password ?? Hash::make(Str::random(40)),
                    'created_at' => $existing?->created_at ?? now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
