<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();
    }

    public function test_user_relations_works(): void
    {

        /** @var User|null */
        $user = User::first();
        $this->assertInstanceOf(Order::class, $user->orders->first());
    }
    public function test_order_relations_works(): void
    {
        /** @var Order|null */
        $order = Order::first();
        $this->assertInstanceOf(User::class, $order->user);
        $this->assertInstanceOf(Product::class, $order->products->first());
    }
    public function test_product_relations_works(): void
    {
        /** @var Product|null */
        $product = Product::first();
        $this->assertInstanceOf(Order::class, $product->orders->first());
    }
}
