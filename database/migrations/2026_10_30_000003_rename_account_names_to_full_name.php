<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Upgrade the original TFA5 tables while preserving existing records.
        Schema::table('customers', function (Blueprint $table): void {
            $table->renameColumn('name', 'full_name');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->renameColumn('name', 'full_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->renameColumn('full_name', 'name');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->renameColumn('full_name', 'name');
        });
    }
};
