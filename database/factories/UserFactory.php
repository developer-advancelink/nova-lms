<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            // Mirrors the column default. Stated explicitly so a factory user
            // is never silently role-less.
            'role' => 'student',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * An administrator — carries no department, by design.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'department' => null,
        ]);
    }

    public function lecturer(string $department = 'SPT'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'lecturer',
            'department' => $department,
        ]);
    }

    public function student(string $department = 'SPT'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
            'department' => $department,
        ]);
    }
}
