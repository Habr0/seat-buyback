<?php

namespace Habr0\Buyback\Database\Seeders;

use Habr0\Buyback\Enums\BasePriceEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class ScheduleSeeder.
 *
 * @package Seat\Web\database\seeds
 */
class BuybackSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('buyback_price_modifiers')->insert([
            'modifier' => 0.95,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('buyback_settings')->insert([
            'base_price' => BasePriceEnum::BUY,
            'instruction' => 'Please set up an <strong>Item Exchange</strong> contract for the corp with the following important parameters:',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
