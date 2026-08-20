<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExceptionLogFactory extends Factory
{
    public function definition(): array
    {
        $types = ['CustomException', 'ValidationException', 'NotFoundException', 'AuthenticationException', 'CriticalException'];

        return [
            'message'         => $this->faker->sentence(),
            'url'             => $this->faker->url(),
            'exception_type'  => $this->faker->randomElement($types),
            'severity'        => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status'          => $this->faker->randomElement(['open', 'investigating', 'resolved']),
            'stack_trace'     => $this->faker->text(500),
            'ip_address'      => $this->faker->ipv4(),
            'user_agent'      => $this->faker->userAgent(),
            'http_method'     => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'request_payload' => ['key' => $this->faker->word()],
            'created_at'      => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
