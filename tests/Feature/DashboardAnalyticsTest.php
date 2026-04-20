<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_dashboard_shows_last_three_days_user_growth(): void
    {
        Carbon::setTestNow('2026-04-20 10:00:00');

        $superAdminRole = Role::query()->create(['name' => 'super_admin']);
        $dashboardPermission = Permission::query()->create(['name' => 'dashboard.view']);
        $superAdminRole->permissions()->sync([$dashboardPermission->id]);

        $superAdmin = User::factory()->create([
            'name' => 'Platform Owner',
            'email' => 'owner@example.com',
        ]);
        $superAdmin->userRoles()->create([
            'role_id' => $superAdminRole->id,
            'company_id' => null,
        ]);

        User::factory()->create(['created_at' => Carbon::now()->subDays(2)->setTime(9, 0)]);
        User::factory()->create(['created_at' => Carbon::now()->subDay()->setTime(11, 30)]);
        User::factory()->create(['created_at' => Carbon::now()->subDay()->setTime(15, 45)]);
        User::factory()->create(['created_at' => Carbon::now()->setTime(8, 15)]);

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('New users added in the last 3 days');
        $response->assertSee('18 Apr', false);
        $response->assertSee('19 Apr', false);
        $response->assertSee('20 Apr', false);
        $response->assertSee('>1<', false);
        $response->assertSee('>2<', false);

        Carbon::setTestNow();
    }
}
