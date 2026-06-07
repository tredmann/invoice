<?php

namespace Database\Factories;

use App\Enums\UnitCode;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Money;
use App\Models\User;
use App\Services\Shared\LineItemProcessorService;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterLineItemFactory extends Factory
{
    protected $model = MasterLineItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'master_invoice_id' => MasterInvoice::inRandomOrder()->first()->id,
            'quantity' => $quantity = $this->faker->randomFloat(2, 2, 1000),
            'price_each' => $priceEach = $this->faker->numberBetween(1, 10000),
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'without_tax' => $withoutTax = LineItemProcessorService::calcWithoutTax($priceEach, $quantity),
            'tax_rate' => $tax_rate = $this->faker->randomElement(Money::DE_TAX_RATES),
            'with_tax' => LineItemProcessorService::calcWithTax($withoutTax, $tax_rate),
            'unit' => $this->faker->randomElement(UnitCode::cases())->value,
            'detail' => $this->faker->words(3, true),
            'detail_plus' => $this->faker->sentences(1, true),
        ];
    }

    public function likeUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'price_each' => $quantity = $this->faker->randomFloat(2, 2, 1000),
            ];
        });
    }
}
