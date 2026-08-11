<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_no_company_can_access_company_creation_form(): void
    {
        $admin = $this->createAdminUser();
        $this->assignPermissionsToRole($this->createRole('admin'), [
            'company.create',
            'company.view',
        ]);

        $this->actingAs($admin)->get(route('companies.create'))->assertOk();
    }

    public function test_admin_can_store_company_and_be_redirected_to_dashboard(): void
    {
        $admin = $this->createAdminUser();
        $this->assignPermissionsToRole($this->createRole('admin'), [
            'company.create',
            'dashboard.view',
        ]);

        $response = $this->actingAs($admin)->post(route('companies.store'), [
            'name' => 'Acme Payroll',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('companies', [
            'name' => 'Acme Payroll',
            'created_by' => $admin->id,
        ]);
    }

    public function test_company_owner_can_edit_update_company(): void
    {
        $admin = $this->createAdminWithCompany([
            'email' => 'owner@example.com',
        ], [
            'name' => 'Owner Co',
            'status' => 'active',
        ]);

        $company = $admin->ownedCompany()->first();
        $this->assignPermissionsToRole($this->createRole('admin'), [
            'company.view',
            'company.edit',
        ]);

        $response = $this->actingAs($admin)->get(route('companies.edit', $company));
        $response->assertOk();

        $update = $this->actingAs($admin)->put(route('companies.update', $company), [
            'name' => 'Owner Co Updated',
            'status' => 'active',
        ]);

        $update->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Owner Co Updated',
        ]);
    }

    public function test_superadmin_can_delete_company(): void
    {
        $superAdmin = $this->createSuperAdminUser([
            'email' => 'superadmin@example.com',
        ]);

        $this->assignPermissionsToRole($this->createRole('super_admin'), [
            'company.delete',
        ]);

        $company = Company::query()->create([
            'name' => 'Delete Co',
            'status' => 'active',
            'created_by' => $superAdmin->id,
        ]);

        $response = $this->actingAs($superAdmin)->delete(route('companies.destroy', $company));

        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
