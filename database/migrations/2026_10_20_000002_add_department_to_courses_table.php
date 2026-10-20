<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the department a module belongs to.
 *
 * Unlike users.department this is NOT NULL: a course with no department is
 * fail-closed for students and lecturers but invisible-yet-unassignable for an
 * admin, which is a silent data-quality trap. Make it impossible instead.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Three steps are all required: a NOT NULL column with no default
        // cannot be added to a populated table, and the step-3 rebuild's
        // INSERT...SELECT fails if any row is still NULL.

        // 1. Add it nullable.
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('department', ['SPT', 'PE'])->nullable()->after('name');
        });

        // 2. Backfill. Both existing courses (CS101, MGMT101) are Dr. Rashid's
        //    and share one enrolled student, so they both go to SPT — splitting
        //    them would manufacture a cross-department enrollment on day one.
        DB::table('courses')->whereNull('department')->update(['department' => 'SPT']);

        // 3. Tighten to NOT NULL.
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('department', ['SPT', 'PE'])->nullable(false)->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['department']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
