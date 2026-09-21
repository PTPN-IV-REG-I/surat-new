<?php

namespace Database\Factories;

use App\Models\SuratUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<SuratUser>
 */
class SuratUserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'director_id' => null,
            'nik' => null,
            'name' => fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->afterCreating(fn (SuratUser $user) => $user->assignRole('admin'));
    }

    public function directorSecretary(): static
    {
        return $this->afterCreating(fn (SuratUser $user) => $user->assignRole('director-secretary'));
    }

    public function departmentHead(): static
    {
        return $this->afterCreating(fn (SuratUser $user) => $user->assignRole('department-head'));
    }

    public function gardenOfficer(): static
    {
        return $this->afterCreating(fn (SuratUser $user) => $user->assignRole('garden-officer'));
    }
}
