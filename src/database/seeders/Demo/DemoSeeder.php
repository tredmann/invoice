<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KranichSoftwareSeeder::class,
            NorthwindCreativeSeeder::class,
        ]);
    }
}
