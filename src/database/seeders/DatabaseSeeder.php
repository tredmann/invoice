<?php

namespace Database\Seeders;

use Database\Seeders\Demo\DemoSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
