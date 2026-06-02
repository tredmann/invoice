<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerMailReceiverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerMailReceiver::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'email' => $this->faker->companyEmail,
            'gender' => ($salutation = $this->faker->randomElement(CustomerMailReceiver::GENDER)),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];
    }
}
