<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_leave_types(): void
    {
        $admin = $this->createAdminWithCompany([
            'email' => 'leave-admin@example.com',
        ], [
            'name' => 'Leave Co',
        ]);

        $this->assignPermissionsToRole($this->createRole('admin'), [
            'leave_type.view',
            'leave_type.create',
            'leave_type.edit',
            'leave_type.delete',
        ]);

        $create = $this->actingAs($admin)->get(route('leave-types.create'));
        $create->assertOk();

        $store = $this->actingAs($admin)->post(route('leave-types.store'), [
            'name' => 'Test Leave',
            'code' => 'TL',
            'default_days' => 10,
            'is_paid' => true,
            'carry_forward' => false,
            'max_carry_forward_days' => 0,
            'requires_approval' => true,
            'description' => 'Test leave type',
        ]);

        $store->assertRedirect(route('leave-types.index'));
        $this->assertDatabaseHas('leave_types', [
            'name' => 'Test Leave',
            'company_id' => $admin->ownedCompany()->value('id'),
        ]);

        $leaveType = LeaveType::query()->where('name', 'Test Leave')->first();

        $edit = $this->actingAs($admin)->get(route('leave-types.edit', $leaveType));
        $edit->assertOk();

        $update = $this->actingAs($admin)->put(route('leave-types.update', $leaveType), [
            'name' => 'Test Leave Updated',
            'code' => 'TL',
            'default_days' => 12,
            'is_paid' => true,
            'carry_forward' => true,
            'max_carry_forward_days' => 5,
            'requires_approval' => true,
            'is_active' => true,
            'description' => 'Updated test leave',
        ]);

        $update->assertRedirect(route('leave-types.index'));
        $this->assertDatabaseHas('leave_types', [
            'id' => $leaveType->id,
            'name' => 'Test Leave Updated',
        ]);

        $destroy = $this->actingAs($admin)->delete(route('leave-types.destroy', $leaveType));
        $destroy->assertRedirect(route('leave-types.index'));
        $this->assertDatabaseMissing('leave_types', ['id' => $leaveType->id]);
    }

    public function test_employee_can_submit_leave_request_and_admin_can_approve_and_reject(): void
    {
        $admin = $this->createAdminWithCompany([
            'email' => 'leave-admin@example.com',
        ], [
            'name' => 'Leave Co',
        ]);

        $employeeUser = $this->createEmployeeUser($admin->ownedCompany()->first(), [
            'email' => 'employee-leave@example.com',
        ]);

        $this->assignPermissionsToRole($this->createRole('employee'), [
            'leave.create',
            'leave.view',
        ]);

        $this->assignPermissionsToRole($this->createRole('admin'), [
            'leave.view',
            'leave.approve',
        ]);

        $leaveType = LeaveType::query()->create([
            'company_id' => $admin->ownedCompany()->value('id'),
            'name' => 'Sick Leave',
            'code' => 'SL',
            'default_days' => 5,
            'is_paid' => true,
            'carry_forward' => false,
            'max_carry_forward_days' => 0,
            'requires_approval' => true,
            'is_active' => true,
            'description' => 'Sick leave',
        ]);

        $this->actingAs($employeeUser)->get(route('leave-requests.create'))->assertOk();

        $store = $this->actingAs($employeeUser)->post(route('leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'session_start' => 'full_day',
            'session_end' => 'full_day',
            'reason' => 'Feeling unwell',
        ]);

        $store->assertRedirect(route('leave-requests.index'));
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employeeUser->employeeProfile->id,
            'leave_type_id' => $leaveType->id,
            'status' => 'pending',
        ]);

        $leaveRequest = LeaveRequest::query()->where('employee_id', $employeeUser->employeeProfile->id)->first();

        $approve = $this->actingAs($admin)->patch(route('leave-requests.approve', $leaveRequest));
        $approve->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved',
        ]);

        $secondLeave = LeaveRequest::query()->create([
            'employee_id' => $employeeUser->employeeProfile->id,
            'leave_type_id' => $leaveType->id,
            'company_id' => $admin->ownedCompany()->value('id'),
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'days_requested' => 2,
            'session_start' => 'full_day',
            'session_end' => 'full_day',
            'reason' => 'Extra sick leave',
            'status' => 'pending',
        ]);

        $reject = $this->actingAs($admin)->patch(route('leave-requests.reject', $secondLeave), [
            'rejection_reason' => 'Not enough coverage',
        ]);

        $reject->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $secondLeave->id,
            'status' => 'rejected',
            'rejection_reason' => 'Not enough coverage',
        ]);
    }
}
