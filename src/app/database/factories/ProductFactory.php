<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/*
$table->char('article', 255)->unique();
$table->char('name', 255);
$table->enum('status', ['available', 'unavailable'])->default('unavailable');
$table->jsonb('data');
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article' => $this->faker->unique()->text(5),
            'name'  => $this->faker->unique()->text(15),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
            'data' => json_encode([
                'key_1' => $this->faker->text(150),
                'key_2' => $this->faker->randomNumber()
            ]),
        ];
    }
}
