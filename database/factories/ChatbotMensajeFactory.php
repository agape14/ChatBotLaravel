<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatbotMensaje>
 */
class ChatbotMensajeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contenido' => $this->faker->sentence(),
            'tipo_mensaje' => $this->faker->randomElement(['text', 'button_reply']),
            'fecha_envio' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
