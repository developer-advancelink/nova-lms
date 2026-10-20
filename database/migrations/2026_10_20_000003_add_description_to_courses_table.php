<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the missing `courses.description` column.
 *
 * The lecturer "Edit Course" modal has always posted a `description` field and
 * PATCH /lecturer/courses/{id} has always written it, but the column never
 * existed and Eloquent silently discarded the value. Admin module management
 * needs the field, so add it and make the existing form work retroactively.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
