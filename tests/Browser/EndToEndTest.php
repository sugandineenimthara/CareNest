<?php
namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;

class EndToEndTest extends DuskTestCase
{
    protected function createUser(string $role): User
    {
        return User::factory()->create([
            'email' => "{$role}@example.com",
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function admin_user_can_navigate_and_manage_children()
    {
        $admin = $this->createUser('admin');
        $this->browse(function (Browser $browser) use ($admin) {
            // Login
            $browser->visit('/login')
                ->type('email', $admin->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/dashboard');

            // Dashboard stats are visible
            $browser->assertSee('Dashboard')
                ->assertVisible('@stats-grid');

            // Go to Children Registry
            $browser->clickLink('Children Registry')
                ->assertPathIs(route('admin.children.index', [], false));

            // Verify stats chips are present
            $browser->assertVisible('.stat-card')
                ->assertSee('Total Registered Children');

            // Open the create child page
            $browser->clickLink('Register New Child')
                ->assertPathIs(route('admin.children.create', [], false));

            // Fill the form (using simple static data)
            $browser->type('child_name', 'Test Child')
                ->type('date_of_birth', '2023-01-01')
                ->select('gender', 'Male')
                ->press('Save')
                ->waitForLocation(route('admin.children.index', [], false))
                ->assertSee('Test Child');

            // Click the first child card to view details
            $browser->click('.child-card a')
                ->assertPathIs(route('admin.children.show', ['child' => Child::first()->child_id], false));

            // Record a vaccine dose
            $browser->press('Record Vaccine Dose')
                ->waitFor('#vaccineModal', 5)
                ->type('vaccine_name', 'Polio')
                ->type('vaccine_date', now()->format('Y-m-d'))
                ->press('Save')
                ->assertDialogOpened('Vaccine recorded')
                ->acceptDialog();

            // Back to children list
            $browser->clickLink('Back to Children')
                ->assertPathIs(route('admin.children.index', [], false));

            // Logout
            $browser->click('@logout-button')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function midwife_user_can_view_and_record_attendance()
    {
        $midwife = $this->createUser('midwife');
        $this->browse(function (Browser $browser) use ($midwife) {
            $browser->visit('/login')
                ->type('email', $midwife->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/dashboard');

            // Navigate to Attendances (midwife prefix)
            $browser->clickLink('Clinic Attendance')
                ->assertPathIs(route('midwife.attendances.index', [], false));

            // Open batch entry modal
            $browser->click('@batch-attendance-button')
                ->waitFor('#batchAttendanceModal')
                ->type('clinic_name', 'Midwife Clinic')
                ->type('clinic_date', now()->format('Y-m-d'))
                ->type('clinic_type', 'mother')
                ->type('attendees', "1,2,3")
                ->press('Save')
                ->assertSee('Clinic attendance recorded');

            $browser->click('@logout-button')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function mother_user_can_view_alerts()
    {
        $mother = $this->createUser('mother');
        // Assume mother profile exists via factory relationships – omitted for brevity.
        $this->browse(function (Browser $browser) use ($mother) {
            $browser->visit('/login')
                ->type('email', $mother->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/dashboard');

            $browser->clickLink('Alerts')
                ->assertPathIs(route('mother.alerts.index', [], false))
                ->assertVisible('.alert-card');

            $browser->click('@logout-button')
                ->assertPathIs('/login');
        });
    }
}
?>
