<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        foreach (['/' => 'A good day starts here.', '/about' => 'About Counter', '/customers' => 'Customer Accounts', '/users' => 'User Accounts'] as $path => $heading) {
            $response = $this->get($path)->assertOk()->assertSee($heading);

            foreach (['home', 'about', 'customers.index', 'users.index'] as $name) {
                $response->assertSee('href="'.route($name).'"', false);
            }
        }
    }

    public function test_customer_page_receives_models_and_renders_seeded_fields(): void
    {
        $response = $this->get('/customers')->assertOk()->assertViewIs('customers.index');
        $records = $response->viewData('customers');

        $this->assertInstanceOf(Collection::class, $records);
        $this->assertInstanceOf(Customer::class, $records->first());
        $this->assertSame([1001, 1002, 1003, 1004, 1005], $records->modelKeys());
        $response->assertSee('Ana Reyes')->assertSee('ana.reyes@example.test')
            ->assertSee('+63 900 000 1001')->assertSee('2026-10-18');
        $this->assertSame(5, substr_count($response->getContent(), '<tr data-record '));
    }

    public function test_user_page_receives_models_and_renders_seeded_fields(): void
    {
        $response = $this->get('/users')->assertOk()->assertViewIs('users.index');
        $records = $response->viewData('users');

        $this->assertInstanceOf(Collection::class, $records);
        $this->assertInstanceOf(User::class, $records->first());
        $this->assertSame([2001, 2002, 2003, 2004, 2005], $records->modelKeys());
        $response->assertSee('Alex Mendoza')->assertSee('alex.mendoza')->assertSee('Administrator');
        $this->assertSame(5, substr_count($response->getContent(), '<tr data-record '));
    }

    public function test_overview_uses_database_counts_and_recent_customers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewHas('customerCount', 5)
            ->assertViewHas('userCount', 5)
            ->assertViewHas('activeUserCount', 4)
            ->assertViewHas('recentCustomers', fn (Collection $records): bool => $records->modelKeys() === [1001, 1002, 1003]);
    }

    public function test_database_edits_and_new_records_appear_in_the_views(): void
    {
        Customer::findOrFail(1001)->update(['full_name' => 'Updated Customer']);
        User::findOrFail(2001)->update(['full_name' => 'Updated Staff', 'status' => 'Inactive']);
        $newCustomer = Customer::create([
            'full_name' => 'Database Only Customer',
            'email' => 'database.only@example.test',
            'phone' => '09123456789',
            'city' => 'Manila',
            'status' => 'Active',
            'joined_on' => '2026-10-30',
        ]);

        $this->get('/customers')->assertOk()->assertSee('Updated Customer')
            ->assertSee('Database Only Customer')->assertDontSee('Ana Reyes');
        $this->get('/users')->assertOk()->assertSee('Updated Staff')->assertDontSee('Alex Mendoza');
        $this->get('/')->assertOk()->assertViewHas('customerCount', 6)
            ->assertViewHas('userCount', 5)->assertViewHas('activeUserCount', 3)
            ->assertViewHas('recentCustomers', fn (Collection $records): bool => $records->first()->is($newCustomer));
    }

    public function test_empty_tables_render_without_sample_array_fallbacks(): void
    {
        Customer::query()->delete();
        User::query()->delete();

        $customers = $this->get('/customers')->assertOk()->assertSee('No customers to display');
        $users = $this->get('/users')->assertOk()->assertSee('No team members to display');
        $this->assertSame(0, substr_count($customers->getContent(), '<tr data-record '));
        $this->assertSame(0, substr_count($users->getContent(), '<tr data-record '));
        $this->get('/')->assertOk()->assertViewHas('customerCount', 0)
            ->assertViewHas('userCount', 0)->assertViewHas('activeUserCount', 0)
            ->assertSee('No customer records yet.');
    }

    public function test_blade_escapes_names_read_from_the_database(): void
    {
        $unsafeName = '<script>alert("test")</script>';
        Customer::findOrFail(1001)->update(['full_name' => $unsafeName]);
        User::findOrFail(2001)->update(['full_name' => $unsafeName]);

        foreach (['/customers', '/users', '/'] as $path) {
            $this->get($path)->assertOk()
                ->assertSee(e($unsafeName), false)->assertDontSee($unsafeName, false);
        }
    }

    public function test_unknown_page_returns_404(): void
    {
        $this->get('/page-that-does-not-exist')->assertNotFound();
    }
}
