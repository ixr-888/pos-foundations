<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        // TFA5 requires a static PHP array rather than a database query.
        $customers = self::sampleRecords();

        return view('customers.index', compact('customers'));
    }

    public static function sampleRecords(): array
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
}
