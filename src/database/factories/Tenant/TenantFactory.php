<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_id' => User::inRandomOrder()->first()->id,
            'name' => ($name = $this->faker->unique()->text(22)),
            'slug' => Str::slug($name),
            'general_info_id' => null,
            'legal_info_id' => null,
        ];
    }
}
