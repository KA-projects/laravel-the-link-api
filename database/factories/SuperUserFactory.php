<?php

namespace Database\Factories;

use App\Models\SuperUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperUser>
 */
class SuperUserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = "123";

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
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (SuperUser $superUser) {
            $superUser->api_token = $superUser->createToken('super_access')->plainTextToken;
            $superUser->save();
        });
    }
}
