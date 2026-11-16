<?php

namespace Tests\Feature;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class AccountNameMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The project's original migrations may already have repaired a legacy
        // schema. Give each scenario canonical columns without earlier history.
        // RefreshDatabase rolls this fixture-only change back after the test.
        if (DB::connection()->getDriverName() !== 'sqlite'
            || DB::connection()->getDatabaseName() !== ':memory:') {
            throw new RuntimeException('These migration fixtures require the isolated in-memory SQLite test database.');
        }
        foreach (['customers', 'users'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'name'), "{$table}.name must exist after the project migrations.");
        }
        Schema::dropIfExists('tsa4_account_name_renames');
    }

    private function repair(): Migration
    {
        return require database_path('migrations/2026_11_16_180000_align_account_name_columns.php');
    }

    private function makeLegacy(string $column): void
    {
        foreach (['customers', 'users'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($column): void {
                $table->renameColumn('name', $column);
            });
        }
    }

    public function test_fresh_schema_and_rollback_are_unchanged(): void
    {
        $this->seed();
        $customers = DB::table('customers')->orderBy('id')->get()->toArray();
        $users = DB::table('users')->orderBy('id')->get()->toArray();
        $this->repair()->up();
        $this->repair()->down();
        $this->assertEquals($customers, DB::table('customers')->orderBy('id')->get()->toArray());
        $this->assertEquals($users, DB::table('users')->orderBy('id')->get()->toArray());
        $this->assertFalse(Schema::hasTable('tsa4_account_name_renames'));
        $this->assertTrue(Schema::hasColumn('customers', 'name'));
        $this->assertTrue(Schema::hasColumn('users', 'name'));
    }

    #[DataProvider('legacyColumns')]
    public function test_legacy_rename_and_rollback_preserve_all_values(string $legacy): void
    {
        $this->seed();
        $customers = DB::table('customers')->orderBy('id')->get()->toArray();
        $users = DB::table('users')->orderBy('id')->get()->toArray();
        $tasks = DB::table('tasks')->orderBy('id')->get()->toArray();
        $this->makeLegacy($legacy);
        $migration = $this->repair();
        $migration->up();
        $migration->up();
        $this->assertDatabaseCount('tsa4_account_name_renames', 2);
        $this->assertEquals($customers, DB::table('customers')->orderBy('id')->get()->toArray());
        $this->assertEquals($users, DB::table('users')->orderBy('id')->get()->toArray());
        $this->assertEquals($tasks, DB::table('tasks')->orderBy('id')->get()->toArray());
        $migration->down();
        foreach (['customers', 'users'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, $legacy));
            $this->assertFalse(Schema::hasColumn($table, 'name'));
        }
        $this->assertSame($customers[0]->name, DB::table('customers')->orderBy('id')->value($legacy));
        $this->assertSame($users[0]->password, DB::table('users')->orderBy('id')->value('password'));
        $this->assertEquals($tasks, DB::table('tasks')->orderBy('id')->get()->toArray());
        $this->assertFalse(Schema::hasTable('tsa4_account_name_renames'));
    }

    public static function legacyColumns(): array
    {
        return [['full_name'], ['fullname']];
    }

    public function test_no_op_up_preserves_existing_history_for_a_new_instance_to_roll_back(): void
    {
        $this->seed();
        $this->makeLegacy('full_name');
        $this->repair()->up();
        $history = DB::table('tsa4_account_name_renames')->orderBy('table_name')->get()->toArray();
        $this->assertCount(2, $history);

        $secondInstance = $this->repair();
        $secondInstance->up();
        $this->assertEquals($history, DB::table('tsa4_account_name_renames')->orderBy('table_name')->get()->toArray());
        $secondInstance->down();

        foreach (['customers', 'users'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'full_name'));
            $this->assertFalse(Schema::hasColumn($table, 'name'));
        }
        $this->assertSame('Ana Reyes', DB::table('customers')->where('id', 1001)->value('full_name'));
        $this->assertSame('Alex Mendoza', DB::table('users')->where('id', 2001)->value('full_name'));
    }

    public function test_unknown_schema_stops_before_changing_either_table(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->renameColumn('name', 'full_name');
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->renameColumn('name', 'display_label');
        });
        try {
            $this->repair()->up();
            $this->fail('The unknown schema should have been rejected.');
        } catch (RuntimeException $error) {
            $this->assertStringContainsString('display_label', $error->getMessage());
        }
        $this->assertTrue(Schema::hasColumn('customers', 'full_name'));
        $this->assertFalse(Schema::hasColumn('customers', 'name'));
        $this->assertTrue(Schema::hasColumn('users', 'display_label'));
        $this->assertFalse(Schema::hasTable('tsa4_account_name_renames'));
    }

    public function test_ambiguous_name_columns_are_not_guessed(): void
    {
        $this->makeLegacy('full_name');
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('fullname')->nullable();
        });
        try {
            $this->repair()->up();
            $this->fail('The ambiguous schema should have been rejected.');
        } catch (RuntimeException $error) {
            $this->assertStringContainsString('Cannot safely align customers', $error->getMessage());
        }
        $this->assertFalse(Schema::hasColumn('customers', 'name'));
        $this->assertTrue(Schema::hasColumn('users', 'full_name'));
        $this->assertFalse(Schema::hasTable('tsa4_account_name_renames'));
    }

    public function test_rollback_preflights_all_tables_before_renaming_any(): void
    {
        $this->makeLegacy('full_name');
        $this->repair()->up();
        Schema::table('users', function (Blueprint $table): void {
            $table->string('full_name')->nullable();
        });
        try {
            $this->repair()->down();
            $this->fail('Rollback should reject the collision.');
        } catch (RuntimeException $error) {
            $this->assertStringContainsString('Inspect the schema', $error->getMessage());
        }
        $this->assertTrue(Schema::hasColumn('customers', 'name'));
        $this->assertFalse(Schema::hasColumn('customers', 'full_name'));
        $this->assertDatabaseCount('tsa4_account_name_renames', 2);
    }
}
