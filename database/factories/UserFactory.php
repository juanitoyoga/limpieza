<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
                'tipo_id' => $this->faker->randomElement(['Cedula', 'Pasaporte']),
                'nro_id' => $this->faker->unique()->numerify('##########'),
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'role' => $this->faker->randomElement(['Admin','Vecino','Supervisor', 'Funcionario']),
                'transition_role' => null,
                'email' => $this->faker->unique()->safeEmail(),
                'password' => 'password', // mutator lo hashea
                'phone' => $this->faker->phoneNumber(),
                'birthdate' => $this->faker->date(),
                'gender' => $this->faker->randomElement(['M','F']),
                'avatar' => null,
                'timezone' => 'America/Guayaquil',
                'language' => 'es',
                'last_login_at' => now(),
                'last_login_ip' => $this->faker->ipv4(),
                'verification_token' => Str::random(60),
                'is_active' => true,
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
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
