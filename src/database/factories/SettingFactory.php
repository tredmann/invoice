<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => ($type = 'string'),
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'key' => $this->faker->word,
            'value' => Crypt::encryptString($this->faker->word),
        ];
    }

    public function integer()
    {
        return $this->state(function () {
            return [
                'type' => 'integer',
                'value' => Crypt::encryptString($this->faker->numberBetween('1', '10000')),
            ];
        });
    }

    public function float()
    {
        return $this->state(function () {
            return [
                'type' => 'float',
                'status' => Crypt::encryptString($this->faker->randomFloat('5')),
            ];
        });
    }

    public function boolean()
    {
        return $this->state(function () {
            return [
                'type' => 'boolean',
                'value' => Crypt::encryptString($this->faker->boolean),
            ];
        });
    }
}
