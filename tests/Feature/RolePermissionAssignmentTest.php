<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_edit_shows_permissions_in_named_groups_with_selected_values_checked(): void
    {
        [$user, $role] = $this->makeSuperAdminWithRoleEditAccess();
        $calendarPermission = Permission::query()->create(['name' => 'leave.calendar.view']);
        Permission::query()->create(['name' => 'leave.approve']);
        Permission::query()->create(['name' => 'payroll.view']);
        Permission::query()->create(['name' => 'view_reports']);
        $role->permissions()->attach($calendarPermission);

        $response = $this->actingAs($user)->get(route('roles.edit', $role));

        $response->assertOk();
        $response->assertSee('Leave');
        $response->assertSee('Calendar View');
        $response->assertSee('Payroll');
        $response->assertSee('General');
        $response->assertSee('View Reports');
        $response->assertSee('value="'.$calendarPermission->id.'"', false);
        $this->assertMatchesRegularExpression(
            '/value="'.$calendarPermission->id.'"\s+checked/s',
            $response->getContent()
        );
        $response->assertSee('name="permission_ids[]"', false);
    }

    public function test_role_edit_saves_the_selected_permission_ids(): void
    {
        [$user, $role] = $this->makeSuperAdminWithRoleEditAccess();
        $calendarPermission = Permission::query()->create(['name' => 'leave.calendar.view']);
        $payrollPermission = Permission::query()->create(['name' => 'payroll.view']);

        $response = $this->actingAs($user)->put(route('roles.update', $role), [
            'name' => $role->name,
            'permission_ids' => [$calendarPermission->id, $payrollPermission->id],
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertEqualsCanonicalizing(
            [$calendarPermission->id, $payrollPermission->id],
            $role->fresh()->permissions()->pluck('permissions.id')->all()
        );
    }

    private function makeSuperAdminWithRoleEditAccess(): array
    {
        $role = Role::query()->create(['name' => 'super_admin']);
        $roleEditPermission = Permission::query()->create(['name' => 'role.edit']);
        $role->permissions()->attach($roleEditPermission);

        $user = User::factory()->create();
        $user->userRoles()->create([
            'role_id' => $role->id,
            'company_id' => null,
        ]);

        return [$user, $role];
    }
}
