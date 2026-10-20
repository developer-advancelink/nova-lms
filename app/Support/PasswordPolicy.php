<?php

namespace App\Support;

use App\Models\User;

/**
 * NOVA's password policy, in one place.
 *
 * This was previously a closure in routes/web.php that read Auth::user() —
 * fine for "change my own password", wrong for an admin setting someone
 * else's initial password. The rules now take the user the password is FOR,
 * not the user performing the action.
 */
class PasswordPolicy
{
    /**
     * @return array<int, mixed>
     */
    public static function rules(?User $for = null, bool $confirmed = true): array
    {
        $rules = [
            'required',
            'min:12',
            'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[0-9]/',
            'regex:/[^A-Za-z0-9]/',
        ];

        if ($confirmed) {
            // Keep 'confirmed' adjacent to the other structural rules so the
            // message keys below line up.
            array_splice($rules, 2, 0, ['confirmed']);
        }

        $rules[] = static::notContainingName($for);

        return $rules;
    }

    /**
     * Reject a password that embeds part of the account holder's own name.
     *
     * A null user (or one with no name yet) skips the check rather than
     * failing closed — there is nothing to compare against.
     */
    public static function notContainingName(?User $for = null): callable
    {
        return function ($attribute, $value, $fail) use ($for) {
            if (! $for || ! $for->name) {
                return;
            }

            $nameParts = array_filter(explode(' ', strtolower($for->name)));

            foreach ($nameParts as $part) {
                if (strlen($part) > 2 && str_contains(strtolower((string) $value), $part)) {
                    $fail('Password must not contain the account holder\'s name.');

                    return;
                }
            }
        };
    }

    /**
     * @return array<string, string>
     */
    public static function messages(string $field = 'password'): array
    {
        return [
            "{$field}.min"       => 'Password must be at least 12 characters.',
            "{$field}.regex"     => 'Password must include uppercase, lowercase, a number, and a symbol.',
            "{$field}.confirmed" => 'Passwords do not match.',
        ];
    }
}
