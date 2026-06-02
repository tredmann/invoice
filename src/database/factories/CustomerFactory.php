<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'company' => $this->faker->company,
            'name' => $this->faker->randomElement([$this->faker->name, null]),
            'street' => $this->faker->streetAddress,
            'postal' => $this->faker->postcode,
            'city' => $this->faker->city,
        ];
    }
}
