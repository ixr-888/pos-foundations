<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['customers', 'users'];
    private const LEGACY_NAMES = ['full_name', 'fullname'];
    private const JOURNAL = 'tsa4_account_name_renames';

    public function up(): void
    {
        $renames = [];

        // Inspect both tables before changing either one.
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Missing {$table} table. Apply the original account migrations first.");
            }
            $columns = Schema::getColumnListing($table);
            if (in_array('name', $columns, true)) {
                continue;
            }
            $aliases = array_values(array_intersect(self::LEGACY_NAMES, $columns));
            if (count($aliases) !== 1) {
                throw new RuntimeException(
                    "Cannot safely align {$table}. Expected one legacy full_name/fullname column. "
                    .'Columns found: '.implode(', ', $columns)
                    .'. The preflight stopped before changing any account columns.'
                );
            }
            $renames[$table] = $aliases[0];
        }

        if ($renames === []) {
            return;
        }

        // Remember only actual renames so rollback never alters a fresh schema.
        if (! Schema::hasTable(self::JOURNAL)) {
            Schema::create(self::JOURNAL, function (Blueprint $table): void {
                $table->string('table_name')->primary();
                $table->string('original_column');
            });
        }

        foreach ($renames as $table => $original) {
            Schema::table($table, function (Blueprint $blueprint) use ($original): void {
                $blueprint->renameColumn($original, 'name');
            });
            DB::table(self::JOURNAL)->updateOrInsert(
                ['table_name' => $table],
                ['original_column' => $original]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::JOURNAL)) {
            return;
        }

        $renames = [];
        foreach (DB::table(self::JOURNAL)->orderBy('table_name')->get() as $entry) {
            $table = $entry->table_name;
            $original = $entry->original_column;
            if (! in_array($table, self::TABLES, true)
                || ! in_array($original, self::LEGACY_NAMES, true)
                || ! Schema::hasTable($table)
                || ! Schema::hasColumn($table, 'name')
                || Schema::hasColumn($table, $original)) {
                throw new RuntimeException('Account columns changed after the repair. Inspect the schema before rolling back.');
            }
            $renames[$table] = $original;
        }

        foreach ($renames as $table => $original) {
            Schema::table($table, function (Blueprint $blueprint) use ($original): void {
                $blueprint->renameColumn('name', $original);
            });
        }
        Schema::dropIfExists(self::JOURNAL);
    }
};
