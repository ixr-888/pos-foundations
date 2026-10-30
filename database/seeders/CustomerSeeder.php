<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'id' => 1001,
                'full_name' => 'Ana Reyes',
                'email' => 'ana.reyes@example.test',
                'phone' => '+63 900 000 1001',
                'city' => 'Quezon City',
                'status' => 'Active',
                'joined_on' => '2026-10-18',
            ],
            [
                'id' => 1002,
                'full_name' => 'Miguel Santos',
                'email' => 'miguel.santos@example.test',
                'phone' => '+63 900 000 1002',
                'city' => 'Makati City',
                'status' => 'Active',
                'joined_on' => '2026-10-15',
            ],
            [
                'id' => 1003,
                'full_name' => 'Sofia Cruz',
                'email' => 'sofia.cruz@example.test',
                'phone' => '+63 900 000 1003',
                'city' => 'Cebu City',
                'status' => 'Active',
                'joined_on' => '2026-10-12',
            ],
            [
                'id' => 1004,
                'full_name' => 'Gabriel Lim',
                'email' => 'gabriel.lim@example.test',
                'phone' => '+63 900 000 1004',
                'city' => 'Davao City',
                'status' => 'Inactive',
                'joined_on' => '2026-10-08',
            ],
            [
                'id' => 1005,
                'full_name' => 'Isabella Flores',
                'email' => 'isabella.flores@example.test',
                'phone' => '+63 900 000 1005',
                'city' => 'Pasig City',
                'status' => 'Active',
                'joined_on' => '2026-10-01',
            ],
        ];

        foreach ($customers as $customer) {
            $account = Customer::findOrNew($customer['id']);
            $account->id = $customer['id'];
            $account->fill($customer);
            $account->save();
        }
    }
}
