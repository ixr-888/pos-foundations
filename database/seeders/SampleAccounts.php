<?php

namespace Database\Seeders;

// Fictional fixture data belongs in the seed layer, not HTTP controllers.
class SampleAccounts
{
    public static function customers(): array
    {
        return [
            [
                'id' => 1001,
                'name' => 'Ana Reyes',
                'email' => 'ana.reyes@example.test',
                'phone' => '+63 900 000 1001',
                'city' => 'Quezon City',
                'status' => 'Active',
                'joined_on' => '2026-10-18',
            ],
            [
                'id' => 1002,
                'name' => 'Miguel Santos',
                'email' => 'miguel.santos@example.test',
                'phone' => '+63 900 000 1002',
                'city' => 'Makati City',
                'status' => 'Active',
                'joined_on' => '2026-10-15',
            ],
            [
                'id' => 1003,
                'name' => 'Sofia Cruz',
                'email' => 'sofia.cruz@example.test',
                'phone' => '+63 900 000 1003',
                'city' => 'Cebu City',
                'status' => 'Active',
                'joined_on' => '2026-10-12',
            ],
            [
                'id' => 1004,
                'name' => 'Gabriel Lim',
                'email' => 'gabriel.lim@example.test',
                'phone' => '+63 900 000 1004',
                'city' => 'Davao City',
                'status' => 'Inactive',
                'joined_on' => '2026-10-08',
            ],
            [
                'id' => 1005,
                'name' => 'Isabella Flores',
                'email' => 'isabella.flores@example.test',
                'phone' => '+63 900 000 1005',
                'city' => 'Pasig City',
                'status' => 'Active',
                'joined_on' => '2026-10-01',
            ],
        ];
    }

    public static function users(): array
    {
        return [
            [
                'id' => 2001,
                'name' => 'Alex Mendoza',
                'username' => 'alex.mendoza',
                'email' => 'alex.mendoza@example.test',
                'role' => 'Administrator',
                'status' => 'Active',
            ],
            [
                'id' => 2002,
                'name' => 'Camille Garcia',
                'username' => 'camille.garcia',
                'email' => 'camille.garcia@example.test',
                'role' => 'Manager',
                'status' => 'Active',
            ],
            [
                'id' => 2003,
                'name' => 'Daniel Torres',
                'username' => 'daniel.torres',
                'email' => 'daniel.torres@example.test',
                'role' => 'Cashier',
                'status' => 'Active',
            ],
            [
                'id' => 2004,
                'name' => 'Lea Navarro',
                'username' => 'lea.navarro',
                'email' => 'lea.navarro@example.test',
                'role' => 'Cashier',
                'status' => 'Inactive',
            ],
            [
                'id' => 2005,
                'name' => 'Marco Rivera',
                'username' => 'marco.rivera',
                'email' => 'marco.rivera@example.test',
                'role' => 'Inventory Staff',
                'status' => 'Active',
            ],
        ];
    }
}
