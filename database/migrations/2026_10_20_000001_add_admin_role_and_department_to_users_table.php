<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Widens `users.role` to include 'admin' and turns the free-text
 * `users.department` into a constrained SPT/PE column.
 *
 * Department values are hardcoded here rather than read from App\Enums\Department
 * so this migration keeps working if that enum later changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Normalise legacy free text. `department` was a profile field users
        //    could not actually write, but anything outside the new value list
        //    would make the rebuild's INSERT...SELECT violate the CHECK
        //    constraint and abort the migration.
        DB::table('users')
            ->whereNotNull('department')
            ->whereNotIn('department', ['SPT', 'PE'])
            ->update(['department' => null]);

        // 2. Both column changes in ONE call so SQLite rebuilds `users` once.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'lecturer', 'admin'])
                ->default('student')
                ->change();

            // Stays nullable: admins belong to no single department. SQLite's
            // CHECK passes on NULL, so nullable and the value list coexist.
            // "Required for non-admins" is enforced in validation instead.
            $table->enum('department', ['SPT', 'PE'])
                ->nullable()
                ->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('department');
        });

        // 3. Backfill existing accounts. Every user predating this migration
        //    belongs to SPT; PE accounts arrive via the seeder as new rows.
        DB::table('users')
            ->whereNull('department')
            ->where('role', '!=', 'admin')
            ->update(['department' => 'SPT']);
    }

    public function down(): void
    {
        // Admin rows would violate the narrowed CHECK constraint when the
        // rebuild copies them, so they have to be downgraded first. This is
        // lossy — an admin comes back as a lecturer.
        DB::table('users')->where('role', 'admin')->update(['role' => 'lecturer']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['department']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'lecturer'])->default('student')->change();
            $table->string('department')->nullable()->change();
        });
    }
};
