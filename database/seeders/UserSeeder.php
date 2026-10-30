<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id' => 2001,
                'full_name' => 'Alex Mendoza',
                'username' => 'alex.mendoza',
                'email' => 'alex.mendoza@example.test',
                'role' => 'Administrator',
                'status' => 'Active',
            ],
            [
                'id' => 2002,
                'full_name' => 'Camille Garcia',
                'username' => 'camille.garcia',
                'email' => 'camille.garcia@example.test',
                'role' => 'Manager',
                'status' => 'Active',
            ],
            [
                'id' => 2003,
                'full_name' => 'Daniel Torres',
                'username' => 'daniel.torres',
                'email' => 'daniel.torres@example.test',
                'role' => 'Cashier',
                'status' => 'Active',
            ],
            [
                'id' => 2004,
                'full_name' => 'Lea Navarro',
                'username' => 'lea.navarro',
                'email' => 'lea.navarro@example.test',
                'role' => 'Cashier',
                'status' => 'Inactive',
            ],
            [
                'id' => 2005,
                'full_name' => 'Marco Rivera',
                'username' => 'marco.rivera',
                'email' => 'marco.rivera@example.test',
                'role' => 'Inventory Staff',
                'status' => 'Active',
            ],
        ];

        foreach ($users as $user) {
            $account = User::findOrNew($user['id']);
            $account->id = $user['id'];
            $account->fill($user);

            // This activity displays staff accounts; it has no login screen.
            // Preserve the existing password hash when running the seeder again.
            if (! $account->exists) {
                $account->password = Hash::make(Str::random(40));
            }

            $account->save();
        }
    }
}
