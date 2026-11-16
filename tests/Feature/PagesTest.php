<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_four_pages_render_and_share_navigation(): void
    {
        foreach (['/' => 'A good day starts here.', '/tasks' => 'Tasks for today.', '/profile' => 'Project profile.', '/about' => 'About Counter.'] as $path => $heading) {
            $response = $this->get($path)->assertOk()->assertSee($heading);

            foreach (['home', 'tasks.index', 'profile', 'about'] as $name) {
                $response->assertSee('href="'.route($name).'"', false);
            }
        }
        $this->assertGuest();
    }

    public function test_customer_page_receives_models_and_renders_seeded_fields(): void
    {
        $response = $this->actingAs(User::findOrFail(2001))->get('/customers')
            ->assertOk()->assertViewIs('customers.index');
        $records = $response->viewData('customers');

        $this->assertInstanceOf(Collection::class, $records);
        $this->assertInstanceOf(Customer::class, $records->first());
        $this->assertSame([1001, 1002, 1003, 1004, 1005], $records->modelKeys());
        $this->assertSame('2026-10-18', $records->first()->joined_on->toDateString());
        $response->assertSee('Ana Reyes')->assertSee('ana.reyes@example.test')
            ->assertSee('+63 900 000 1001')->assertSee('Oct 18, 2026');
        $this->assertSame(5, substr_count($response->getContent(), '<tr data-record '));
    }

    public function test_user_page_receives_models_and_renders_seeded_fields(): void
    {
        $response = $this->actingAs(User::findOrFail(2001))->get('/users')
            ->assertOk()->assertViewIs('users.index');
        $records = $response->viewData('users');

        $this->assertInstanceOf(Collection::class, $records);
        $this->assertInstanceOf(User::class, $records->first());
        $this->assertSame([2001, 2002, 2003, 2004, 2005], $records->modelKeys());
        $response->assertSee('Alex Mendoza')->assertSee('alex.mendoza')->assertSee('Administrator');
        $this->assertSame(5, substr_count($response->getContent(), '<tr data-record '));
    }

    public function test_overview_uses_database_task_counts_and_upcoming_tasks(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewHas('taskCount', 5)
            ->assertViewHas('openCount', 4)
            ->assertViewHas('doneCount', 1)
            ->assertViewHas('todayCount', 3)
            ->assertViewHas('upcomingTasks', fn (Collection $records): bool => $records->modelKeys() === [3001, 3002, 3003, 3005]);
    }

    public function test_database_edits_and_new_records_appear_in_the_views(): void
    {
        // Use another seeded member so the header cannot retain the edited name.
        $this->actingAs(User::findOrFail(2002));
        Customer::findOrFail(1001)->update(['name' => 'Updated Customer']);
        User::findOrFail(2001)->update(['name' => 'Updated Staff', 'status' => 'Inactive']);
        $newCustomer = Customer::create([
            'name' => 'Database Only Customer',
            'email' => 'database.only@example.test',
            'phone' => '09123456789',
            'city' => 'Manila',
            'status' => 'Active',
            'joined_on' => '2026-10-30',
        ]);

        $customers = $this->get('/customers')->assertOk()->assertSee('Updated Customer')
            ->assertSee('Database Only Customer')->assertDontSee('Ana Reyes');
        $this->assertTrue($customers->viewData('customers')->contains($newCustomer));
        $this->get('/users')->assertOk()->assertSee('Updated Staff')->assertDontSee('Alex Mendoza');

        Task::findOrFail(3001)->update(['title' => 'Updated Opening Task']);
        $newTask = Task::create([
            'title' => 'Database Only Task',
            'description' => 'A task added directly to the database.',
            'due_date' => today()->subDay()->toDateString(),
            'priority' => 'high',
            'status' => 'pending',
        ]);
        $this->get('/')->assertOk()->assertViewHas('taskCount', 6)
            ->assertViewHas('openCount', 5)->assertViewHas('doneCount', 1)
            ->assertViewHas('upcomingTasks', fn (Collection $records): bool => $records->first()->is($newTask))
            ->assertSee('Updated Opening Task')->assertSee('Database Only Task');
    }

    public function test_empty_tables_render_without_sample_array_fallbacks(): void
    {
        $this->actingAs(User::findOrFail(2001));
        Customer::query()->delete();

        $customers = $this->get('/customers')->assertOk()->assertSee('No customers to display');
        $this->assertSame(0, substr_count($customers->getContent(), '<tr data-record '));

        Auth::logout();
        User::query()->delete();
        $this->assertDatabaseCount('users', 0);

        // With no users there is no valid login; test the empty view directly
        // and independently retain the route's guest protection.
        $users = view('users.index', ['users' => User::query()->get()])->render();
        $this->assertStringContainsString('No team members to display', $users);
        $this->assertSame(0, substr_count($users, '<tr data-record '));
        $this->get('/users')->assertRedirect(route('login'));

        Task::query()->delete();
        $this->get('/')->assertOk()->assertViewHas('taskCount', 0)
            ->assertViewHas('openCount', 0)->assertViewHas('doneCount', 0)
            ->assertSee('There are no unfinished tasks.');
    }

    public function test_blade_escapes_names_read_from_the_database(): void
    {
        $this->actingAs(User::findOrFail(2002));
        $unsafeName = '<script>alert("test")</script>';
        Customer::findOrFail(1001)->update(['name' => $unsafeName]);
        User::findOrFail(2001)->update(['name' => $unsafeName]);
        Task::findOrFail(3001)->update(['title' => $unsafeName]);

        foreach (['/customers', '/users', '/', '/tasks'] as $path) {
            $this->get($path)->assertOk()
                ->assertSee(e($unsafeName), false)->assertDontSee($unsafeName, false);
        }
    }

    public function test_unknown_page_returns_404(): void
    {
        $this->get('/page-that-does-not-exist')->assertNotFound();
    }
}
