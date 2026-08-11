<?php

namespace Tests\Feature;

use App\Models\SalaryStructure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryStructureEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_salary_structures(): void
    {
        $admin = $this->createAdminWithCompany([
            'email' => 'salary-admin@example.com',
        ], [
            'name' => 'Salary Co',
        ]);

        $this->assignPermissionsToRole($this->createRole('admin'), [
            'salary_structure.view',
            'salary_structure.create',
            'salary_structure.edit',
            'salary_structure.delete',
        ]);

        $index = $this->actingAs($admin)->get(route('salary-structures.index'));
        $index->assertOk();

        $store = $this->actingAs($admin)->post(route('salary-structures.store'), [
            'name' => 'Standard Plan',
            'base_salary' => 50000,
            'hra' => 10000,
            'allowances' => 5000,
            'deductions' => 2000,
        ]);

        $store->assertRedirect(route('salary-structures.index'));
        $this->assertDatabaseHas('salary_structures', [
            'name' => 'Standard Plan',
            'company_id' => $admin->ownedCompany()->value('id'),
        ]);

        $salaryStructure = SalaryStructure::query()->where('name', 'Standard Plan')->first();

        $edit = $this->actingAs($admin)->get(route('salary-structures.edit', $salaryStructure));
        $edit->assertOk();

        $update = $this->actingAs($admin)->put(route('salary-structures.update', $salaryStructure), [
            'name' => 'Standard Plan',
            'base_salary' => 52000,
            'hra' => 12000,
            'allowances' => 6000,
            'deductions' => 2500,
        ]);

        $update->assertRedirect(route('salary-structures.index'));
        $this->assertDatabaseHas('salary_structures', [
            'id' => $salaryStructure->id,
            'base_salary' => 52000,
        ]);

        $destroy = $this->actingAs($admin)->delete(route('salary-structures.destroy', $salaryStructure));
        $destroy->assertRedirect(route('salary-structures.index'));
        $this->assertDatabaseMissing('salary_structures', ['id' => $salaryStructure->id]);
    }
}
