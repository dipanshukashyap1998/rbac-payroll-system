<?php

namespace Database\Seeders;

use App\Models\ProfessionalTax;
use Illuminate\Database\Seeder;

class ProfessionalTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Indian Professional Tax by state (2024-2025)
     */
    public function run(): void
    {
        ProfessionalTax::truncate();

        $professionalTaxes = [
            // Maharashtra
            ['state' => 'Maharashtra', 'income_from' => 0, 'income_to' => 500000, 'tax_amount' => 0],
            ['state' => 'Maharashtra', 'income_from' => 500000, 'income_to' => 750000, 'tax_amount' => 2500],
            ['state' => 'Maharashtra', 'income_from' => 750000, 'income_to' => 1000000, 'tax_amount' => 5000],
            ['state' => 'Maharashtra', 'income_from' => 1000000, 'income_to' => 9999999999, 'tax_amount' => 7500],
            // Karnataka
            ['state' => 'Karnataka', 'income_from' => 0, 'income_to' => 600000, 'tax_amount' => 0],
            ['state' => 'Karnataka', 'income_from' => 600000, 'income_to' => 1000000, 'tax_amount' => 3000],
            ['state' => 'Karnataka', 'income_from' => 1000000, 'income_to' => 9999999999, 'tax_amount' => 6000],
            // Tamil Nadu
            ['state' => 'Tamil Nadu', 'income_from' => 0, 'income_to' => 600000, 'tax_amount' => 0],
            ['state' => 'Tamil Nadu', 'income_from' => 600000, 'income_to' => 1800000, 'tax_amount' => 3000],
            ['state' => 'Tamil Nadu', 'income_from' => 1800000, 'income_to' => 9999999999, 'tax_amount' => 6000],
            // Delhi
            ['state' => 'Delhi', 'income_from' => 0, 'income_to' => 500000, 'tax_amount' => 0],
            ['state' => 'Delhi', 'income_from' => 500000, 'income_to' => 1000000, 'tax_amount' => 2500],
            ['state' => 'Delhi', 'income_from' => 1000000, 'income_to' => 9999999999, 'tax_amount' => 5000],
            // Gujarat
            ['state' => 'Gujarat', 'income_from' => 0, 'income_to' => 1000000, 'tax_amount' => 0],
            ['state' => 'Gujarat', 'income_from' => 1000000, 'income_to' => 9999999999, 'tax_amount' => 5000],
            // Default for other states
            ['state' => 'General', 'income_from' => 0, 'income_to' => 500000, 'tax_amount' => 0],
            ['state' => 'General', 'income_from' => 500000, 'income_to' => 1000000, 'tax_amount' => 2000],
            ['state' => 'General', 'income_from' => 1000000, 'income_to' => 9999999999, 'tax_amount' => 5000],
        ];

        foreach ($professionalTaxes as $tax) {
            ProfessionalTax::create(array_merge($tax, ['is_active' => true]));
        }
    }
}
