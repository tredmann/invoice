<?php

namespace Database\Factories;

use App\Enums\UnitCode;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Money;
use App\Models\User;
use App\Services\Shared\LineItemProcessorService;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LineItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invoice_id' => Invoice::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'quantity' => $quantity = $this->faker->randomFloat(2, 1, 1000),
            'price_each' => $priceEach = $this->faker->numberBetween(1, 10000),
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'tax_rate' => $tax_rate = $this->faker->randomElement(Money::DE_TAX_RATES),
            'without_tax' => $withoutTax = LineItemProcessorService::calcWithoutTax($priceEach, $quantity),
            'with_tax' => LineItemProcessorService::calcWithTax($withoutTax, $tax_rate),
            'unit' => $this->faker->randomElement(UnitCode::cases())->value,
            'detail' => $this->faker->words(3, true),
            'detail_plus' => $this->faker->sentences(1, true),
        ];
    }
}
