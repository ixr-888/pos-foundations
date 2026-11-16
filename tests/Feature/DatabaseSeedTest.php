<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_and_seeds_include_the_required_customer_and_staff_fields(): void
    {
        $this->seed();

        $this->assertTrue(Schema::hasColumns('customers', ['id', 'name', 'email', 'phone']));
        $this->assertTrue(Schema::hasColumns('users', ['id', 'username', 'name']));
        $this->assertFalse(Schema::hasColumn('customers', 'full_name'));
        $this->assertFalse(Schema::hasColumn('users', 'full_name'));
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('customers', [
            'id' => 1001, 'name' => 'Ana Reyes',
            'email' => 'ana.reyes@example.test', 'phone' => '+63 900 000 1001',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => 2001, 'name' => 'Alex Mendoza', 'username' => 'alex.mendoza',
        ]);
        $this->assertDatabaseHas('users', ['id' => 2005, 'name' => 'Marco Rivera']);

        foreach (User::all() as $user) {
            $this->assertNotEmpty(password_get_info($user->password)['algo']);
        }
    }

    public function test_seeding_again_does_not_duplicate_accounts_or_reset_passwords(): void
    {
        $this->seed();
        $account = User::findOrFail(2001);
        $account->password = Hash::make('Local-test-password');
        $account->save();
        $password = $account->password;
        $createdAt = $account->getRawOriginal('created_at');

        $this->seed();

        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertSame($password, $account->fresh()->password);
        $this->assertSame($createdAt, $account->fresh()->getRawOriginal('created_at'));
    }

    public function test_username_uniqueness_is_enforced_by_the_database(): void
    {
        $this->seed();
        $this->expectException(QueryException::class);
        // Confirm the failure is the username constraint, not a missing field.
        $this->expectExceptionMessage('UNIQUE constraint failed: users.username');

        User::create([
            'name' => 'Duplicate Username',
            'username' => 'alex.mendoza',
            'email' => 'different.staff@example.test',
            'role' => 'Cashier',
            'status' => 'Active',
            'password' => Hash::make('Local-test-password'),
        ]);
    }

    public function test_name_upgrade_and_rollback_preserve_existing_records(): void
    {
        $this->seed();
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // Build an independent legacy fixture inside RefreshDatabase's transaction.
        Schema::dropIfExists('tsa4_account_name_renames');
        foreach (['customers', 'users'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->renameColumn('name', 'full_name');
            });
        }
        DB::table('customers')->where('id', 1001)->update(['full_name' => 'Existing Customer']);
        DB::table('users')->where('id', 2001)->update(['full_name' => 'Existing Staff']);
        $password = DB::table('users')->where('id', 2001)->value('password');
        $createdAt = DB::table('users')->where('id', 2001)->value('created_at');

        $migration = require database_path('migrations/2026_11_16_180000_align_account_name_columns.php');
        $migration->up();
        $this->assertDatabaseHas('customers', ['id' => 1001, 'name' => 'Existing Customer']);
        $this->assertDatabaseHas('users', ['id' => 2001, 'name' => 'Existing Staff']);
        $this->assertSame($password, DB::table('users')->where('id', 2001)->value('password'));
        $this->assertSame($createdAt, DB::table('users')->where('id', 2001)->value('created_at'));

        $migration->down();
        $this->assertDatabaseHas('customers', ['id' => 1001, 'full_name' => 'Existing Customer']);
        $this->assertDatabaseHas('users', ['id' => 2001, 'full_name' => 'Existing Staff']);
        $this->assertSame($password, DB::table('users')->where('id', 2001)->value('password'));
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);

        $migration->up();
        $this->assertDatabaseHas('customers', ['id' => 1001, 'name' => 'Existing Customer']);
        $this->assertDatabaseHas('users', ['id' => 2001, 'name' => 'Existing Staff']);
    }
}
