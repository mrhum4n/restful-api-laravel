<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        
        return [
            'first_name' => $faker->firstName,
            'last_name' =>$faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'phone' => $faker->unique()->phoneNumber
        ];
    }
}
