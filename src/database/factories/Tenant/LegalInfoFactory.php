<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\LegalInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegalInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegalInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'registry_court' => $this->faker->word,
            'registry_no' => $this->faker->word,
            'company_owner' => $this->faker->word,
            'tax_no' => $this->faker->word,
            'vat_no' => $this->faker->word,
            'swift_bic' => $this->faker->word,
            'iban' => $this->faker->word,
            'bank_institute' => $this->faker->word,
        ];
    }
}
