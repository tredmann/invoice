<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\GeneralInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneralInfoFactory extends Factory
{
    protected $model = GeneralInfo::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'owner' => $this->faker->name,
            'additional_address' => $this->faker->randomElement([null, $this->faker->companySuffix]),
            'street' => $this->faker->streetAddress,
            'postal' => $this->faker->postcode,
            'city' => $this->faker->city,
            'country' => 'Deutschland',
            'fax' => '+49'.$this->faker->randomDigit,
            'email' => $this->faker->email,
            'homepage' => $this->faker->url,
        ];
    }
}
