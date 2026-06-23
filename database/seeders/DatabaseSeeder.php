<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            TaxSlabSeeder::class,
            ProfessionalTaxSeeder::class,
            UserSeeder::class,
        ]);
    }
}
