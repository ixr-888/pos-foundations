<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        // These fictional accounts are displayed from a static PHP array.
        $users = self::sampleRecords();

        return view('users.index', compact('users'));
    }

    public static function sampleRecords(): array
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
