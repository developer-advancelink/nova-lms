<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * A module (the UI calls these "modules"; the table has always said courses).
 *
 * VISIBILITY RULE — read this before adding a query.
 *
 * Every Course query reachable from an HTTP request must go through
 * scopeVisibleTo(), which enforces both department and ownership. Routes that
 * declare a {course} parameter get this for free via resolveRouteBinding().
 * The ONLY code allowed to query courses unscoped is App\Http\Controllers\Admin,
 * because an admin legitimately sees every department — and visibleTo() already
 * returns everything for an admin, so even those should just call it.
 *
 * Seeders, tinker and console code are intentionally unaffected: the scope is
 * opt-in at the query level and route binding only runs inside the router.
 */
class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'department',
        'lecturer_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Visibility
    |--------------------------------------------------------------------------
    */

    /**
     * Restrict a query to the modules this user may see.
     *
     * "Visible" means same department AND (owns it, as a lecturer / is enrolled
     * in it, as a student). Admins see everything; everyone else fails closed.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        // Fail closed for guests, and for any non-admin with no department.
        // This MUST come before the where() below: Eloquent compiles
        // where('department', null) to "department is null", which would match
        // rather than exclude.
        if (! $user || (! $user->isAdmin() && $user->department === null)) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        // Redundant when the data is consistent, kept deliberately: if an admin
        // ever assigns a module to a lecturer in the other department, this
        // fails closed instead of leaking.
        $query->where($this->qualifyColumn('department'), $user->department);

        return $user->isLecturer()
            ? $query->where($this->qualifyColumn('lecturer_id'), $user->id)
            : $query->whereHas('enrollments', fn ($q) => $q->where('user_id', $user->id));
    }

    /**
     * Scope every {course} route parameter automatically.
     *
     * This is the load-bearing piece: a route that type-hints Course is scoped
     * whether or not whoever wrote it remembered to think about it. Returning
     * null makes the router raise a 404 rather than a 403, so there is no
     * existence oracle for modules in the other department.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return static::visibleTo(Auth::user())
            ->where($this->qualifyColumn($field ?? $this->getRouteKeyName()), $value)
            ->first();
    }

    /**
     * The department as an enum case, for labels and pills in the views.
     * The column itself stays a plain string — see the note on the enum.
     */
    public function departmentEnum(): ?\App\Enums\Department
    {
        return \App\Enums\Department::tryFromValue($this->department);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class)->latest();
    }
}