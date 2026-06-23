<?php

namespace Database\Seeders;

use App\Models\TaxSlab;
use Illuminate\Database\Seeder;

class TaxSlabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Indian Income Tax Slabs for AY 2026-27 (new tax regime under section 115BAC)
     */
    public function run(): void
    {
        TaxSlab::truncate();

        $taxSlabs = [
            ['income_from' => 0, 'income_to' => 400000, 'tax_rate' => 0, 'description' => 'No tax up to 4 lakhs'],
            ['income_from' => 400000, 'income_to' => 800000, 'tax_rate' => 5, 'description' => '5% on income between 4 to 8 lakhs'],
            ['income_from' => 800000, 'income_to' => 1200000, 'tax_rate' => 10, 'description' => '10% on income between 8 to 12 lakhs'],
            ['income_from' => 1200000, 'income_to' => 1600000, 'tax_rate' => 15, 'description' => '15% on income between 12 to 16 lakhs'],
            ['income_from' => 1600000, 'income_to' => 2000000, 'tax_rate' => 20, 'description' => '20% on income between 16 to 20 lakhs'],
            ['income_from' => 2000000, 'income_to' => 2400000, 'tax_rate' => 25, 'description' => '25% on income between 20 to 24 lakhs'],
            ['income_from' => 2400000, 'income_to' => 9999999999, 'tax_rate' => 30, 'description' => '30% on income above 24 lakhs'],
        ];

        foreach ($taxSlabs as $slab) {
            TaxSlab::create(array_merge($slab, ['is_active' => true]));
        }
    }
}
