<?php

namespace Tests\Feature;

use App\Category;
use App\Order;
use App\Product;
use App\Rate;
use App\User;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\CategoryFactory;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUserDashboardRequiresLoggin()
    {
        $user = factory(User::class)->create();

        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/profile')
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    public function testUserCanAccessOnlyHisProfile()
    {
        $user = $this->signIn();
        $user2 = factory(User::class)->create();

        $this->get('/' . app()->getLocale() . '/user/' . $user2->id . '/profile')
            ->assertStatus(403);
    }

    public function testUserHasDashboard()
    {
        $user = $this->signIn();

        $user->orders()->saveMany(
            factory(Order::class, 5)->make()
        );

        factory(Product::class, 30)->create([
            'user_id' => $user->id
        ]);

        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/profile')
            ->assertOk()
            ->assertSee(5)
            ->assertSee(30);
    }

    public function testUserCanSeeHisOrders()
    {
        $user = $this->signIn();

        $c = factory(Category::class)->create();
        $sc = factory(Category::class)->create([
            'category_id' => $c
        ]);

        /** @var \App\Product[] $products */
        $products = $sc->products()->saveMany(
            factory(Product::class, 70)->make([
                'user_id' => $user->id,
                'category_slug' => $sc->slug
            ])
        );

        $orders = $user->orders()->saveMany(
            factory(Order::class, 80)->make([
                'product_id' => $products[0]->id
            ])
        );

        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/orders')
            ->assertOk()
            ->assertSee(Order::latest()->first()->address)
            ->assertSee($orders->find(20)->address)
            ->assertDontSee($orders->find(70)->address)
            ->assertSee("page-item");

        // visit page two
        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/orders?page=2')
            ->assertOk()
            ->assertSee($orders->find(70)->address)
            ->assertDontSee($orders->find(30)->address)
            ->assertSee('page-item');
    }

    public function testUserCanSeeHisProducts()
    {
        // $this->withoutExceptionHandling();
        $user = $this->signIn();

        $c = factory(Category::class)->create();
        $sc = factory(Category::class)->create([
            'category_id' => $c
        ]);

        $products = $sc->products()->saveMany(
            factory(Product::class, 70)->make([
                'user_id' => $user->id,
                'category_slug' => $sc->slug
            ])
        );

        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/products')
            ->assertOk()
            ->assertDontSee($products->find(70)->name)
            ->assertSee("page-item");

        // visit page two
        $this->get('/' . app()->getLocale() . '/user/' . $user->id . '/products?page=2')
            ->assertOk()
            ->assertSee($products->find(70)->name)
            ->assertDontSee($products->find(30)->name)
            ->assertSee('page-item');
    }

    public function testAdminCanSeeSiteStats()
    {
        $user = $this->signIn([
            'role' => User::AdminRole
        ]);

        factory(Product::class, 20)->create();
        factory(Order::class, 15)->create();
        factory(User::class, 30)->create();
        factory(Rate::class, 70)->create();

        $this->get('/en/user/' . $user->id . '/profile')
            ->assertSee(20)
            ->assertSee(15)
            ->assertSee(30)
            ->assertSee(70);
    }

    public function testOnlyAdminCanAccessUsersPage()
    {
        $user = $this->signIn();

        $this->get('/en/user/' . $user->id . '/users')
            ->assertStatus(403);
    }

    public function testAdminCanSeeUsersTable()
    {
        // $this->withoutExceptionHandling();

        $user = $this->signIn(['role' => User::AdminRole]);

        $user2 = factory(User::class)->create();
        $user2->products()->saveMany(
            factory(Product::class, 80)->make()
        );


        $this->get('/en/user/' . $user->id . '/users')
            ->assertOk()
            ->assertSee("<td>80</td>", false)
            ->assertSee('page-item');
    }

    public function testNotAdminUsersCanNotChangeRole()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->postJson('/api/user/' . $user->id . '/role/up')
            ->assertStatus(403);

        $this->signIn(['role' => User::SuperRole]);
        $this->postJson('/api/user/' . $user->id . '/role/up')
            ->assertStatus(403);
    }

    public function testOnlyAdminCanUpdateUsersRole()
    {
        $this->withoutExceptionHandling();

        $admin = $this->signIn(['role' => User::AdminRole]);

        $user = factory(User::class)->create();

        $this->assertFalse($user->isSuper());

        $this->postJson('/api/user/' . $user->id . '/role/up', ['super' => true])
            ->assertOk()
            ->assertExactJson(['updated' => true]);

        $user = User::find($user->id);

        $this->assertTrue($user->isSuper());

        $this->postJson('/api/user/' . $user->id . '/role/up', ['super' => false])
            ->assertOk()
            ->assertExactJson(['updated' => true]);

        $user = User::find($user->id);

        $this->assertFalse($user->isSuper());
    }
}
