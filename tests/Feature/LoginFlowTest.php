<?php

namespace Tests\Feature;

use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_root_to_login(): void
    {
        $this->get('/surat')->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
    }

    public function test_login_with_valid_credentials_forces_password_change_first(): void
    {
        $user = SuratUser::factory()->create([
            'username' => 'kebun01',
            'password' => bcrypt('secret123'),
            'must_change_password' => true,
        ]);
        $user->assignRole('garden-officer');

        $response = $this->post(route('login'), [
            'username' => 'kebun01',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('password.change'));

        // Belum ganti password -> dashboard tetap dilempar balik ke password.change
        $this->get(route('dashboard'))->assertRedirect(route('password.change'));

        $this->put(route('password.update'), [
            'current_password' => 'secret123',
            'password' => 'newSecret456',
            'password_confirmation' => 'newSecret456',
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse($user->fresh()->must_change_password);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        SuratUser::factory()->create([
            'username' => 'kebun02',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'username' => 'kebun02',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        SuratUser::factory()->create([
            'username' => 'nonaktif',
            'password' => bcrypt('secret123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'username' => 'nonaktif',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_active_user_deactivated_mid_session_is_logged_out_on_next_request(): void
    {
        $user = SuratUser::factory()->create(['must_change_password' => false]);
        $user->assignRole('garden-officer');

        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $user->update(['is_active' => false]);

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $user = SuratUser::factory()->create(['must_change_password' => false]);
        $user->assignRole('garden-officer');

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'newuser',
            'name' => 'User Baru',
            'role' => 'garden-officer',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('surat_users', ['username' => 'newuser']);
        $this->assertTrue(SuratUser::where('username', 'newuser')->first()->hasRole('garden-officer'));
    }
}
