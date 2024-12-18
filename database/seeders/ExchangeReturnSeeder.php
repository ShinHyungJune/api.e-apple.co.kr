<?php

namespace Database\Seeders;

use App\Models\ExchangeReturn;
use Illuminate\Database\Seeder;

class ExchangeReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeReturn::factory(50)->create();
    }
}
