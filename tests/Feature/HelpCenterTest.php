<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_help_center(): void
    {
        $this->get(route('help.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_read_the_product_guide(): void
    {
        $user = $this->createAdminWithCompany();

        $this->actingAs($user)
            ->get(route('help.index'))
            ->assertOk()
            ->assertSee('Register an admin account')
            ->assertSee('Add an employee account')
            ->assertSee('Attendance and Payable Days')
            ->assertSee('not currently operational');
    }
}
