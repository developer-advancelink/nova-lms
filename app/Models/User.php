<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'id_number',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    | `role` stays a plain string attribute on purpose — it is compared to raw
    | strings in the layouts, sidebar, footer, middleware and route closures,
    | so an enum cast would make every `=== 'lecturer'` silently false.
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Where this user lands after signing in, and where a wrong-role redirect
     * sends them. Every role must have an entry here or the middleware loops.
     */
    public function homePath(): string
    {
        return match ($this->role) {
            'admin'    => '/admin/dashboard',
            'lecturer' => '/lecturer/dashboard',
            default    => '/dashboard',
        };
    }

    /**
     * This user's own profile screen. Needed because the shared profile view
     * posts back to a role-specific endpoint.
     */
    public function accountPath(): string
    {
        return match ($this->role) {
            'admin'    => '/admin/account',
            'lecturer' => '/lecturer/account',
            default    => '/account',
        };
    }

    /**
     * The department as an enum case, or null for admins (and for any row that
     * predates the department column).
     */
    public function departmentEnum(): ?\App\Enums\Department
    {
        return \App\Enums\Department::tryFromValue($this->department);
    }

    /**
     * Plural, title-cased role name — the heading each group of accounts sits
     * under on the admin accounts screen.
     */
    public function roleGroupLabel(): string
    {
        return match ($this->role) {
            'admin'    => 'Administrators',
            'lecturer' => 'Lecturers',
            default    => 'Students',
        };
    }

    /**
     * Each role gets its own colour. Students and lecturers previously shared
     * one grey pill, which is what made the three hard to tell apart at a
     * glance.
     */
    public function rolePillClasses(): string
    {
        return match ($this->role) {
            'admin'    => 'bg-amber-50 text-amber-700',
            'lecturer' => 'bg-violet-50 text-violet-700',
            default    => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * Sort order for grouping: students, then lecturers, then admins.
     * Written as a CASE so it works on SQLite without a lookup table.
     */
    public static function roleOrderSql(): string
    {
        return "case role when 'student' then 1 when 'lecturer' then 2 when 'admin' then 3 else 4 end";
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}