<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatbotUsuario>
 */
class ChatbotUsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_telefono' => $this->faker->phoneNumber(),
            'nombre' => $this->faker->name(),
            'ultima_interaccion' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
