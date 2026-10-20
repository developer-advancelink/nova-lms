<?php

namespace App\Enums;

/**
 * The departments NOVA serves.
 *
 * This is the single source of truth for the department pair. It backs
 * validation rules, admin dropdowns and table pills.
 *
 * Note: `users.department` and `courses.department` are deliberately NOT cast
 * to this enum. Both columns are compared to raw strings throughout the Blade
 * views and middleware, and a backed enum would make every `=== 'SPT'` false
 * and throw when echoed with `{{ }}`.
 */
enum Department: string
{
    case SPT = 'SPT';
    case PE = 'PE';

    /**
     * Values for Rule::in() and migration enum definitions.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * The full name, for dropdowns and headings.
     */
    public function label(): string
    {
        return match ($this) {
            self::SPT => 'Sekolah Pertukangan Tentera (SPT)',
            self::PE => 'Pusat Evolusi (PE)',
        };
    }

    /**
     * The short name, for table pills where space is tight.
     */
    public function shortLabel(): string
    {
        return $this->value;
    }

    /**
     * Tailwind classes for this department's pill, matching the palette the
     * rest of the app uses for badges.
     */
    public function pillClasses(): string
    {
        return match ($this) {
            self::SPT => 'bg-sky-50 text-sky-700',
            self::PE => 'bg-emerald-50 text-emerald-700',
        };
    }

    /**
     * Resolve a stored string to a case, or null if it is absent/unrecognised.
     */
    public static function tryFromValue(?string $value): ?self
    {
        return $value === null ? null : self::tryFrom($value);
    }
}
