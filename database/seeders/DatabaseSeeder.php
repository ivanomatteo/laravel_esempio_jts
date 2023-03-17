<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.local',
        ]);

        $users = \App\Models\User::factory(10)->withPersonalTeam()->create();
        $users->push($user);

        $products = Product::factory(100)->create();

        $users->each(function (User $user) use ($products) {
            Order::factory(10)->create(['user_id' => $user->id])
                ->each(function (Order $o) use ($products) {
                    $o->products()
                        ->sync(
                            $products->random(5)
                                ->pluck('id')
                        );
                });
        });
    }
}
