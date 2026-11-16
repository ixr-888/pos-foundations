<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Seed the fictional sample directory.
        foreach (SampleAccounts::customers() as $customer) {
            $existing = DB::table('customers')->where('id', $customer['id'])->first();

            DB::table('customers')->updateOrInsert(
                ['id' => $customer['id']],
                array_merge($customer, [
                    'created_at' => $existing?->created_at ?? now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
