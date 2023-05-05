<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $domain = 'dev.local';

            /** @var User */
            $admin = User::factory()
                ->withPersonalTeam()
                ->create([
                    'name' => 'Admin',
                    'email' => "admin@$domain",
                ]);

            /** @var User */
            $user = User::factory()
                ->withPersonalTeam()
                ->create([
                    'name' => 'User',
                    'email' => "user@$domain",
                ]);

            $users = \App\Models\User::factory(5)->withPersonalTeam()->create();
            $users->push($user);

            $products = Product::factory(200)->create();

            $users->each(function (User $user) use ($products) {
                Order::factory(50)->create(['user_id' => $user->id])
                    ->each(function (Order $o) use ($products) {
                        $o->products()
                            ->sync(
                                $products->random(5)
                                    ->pluck('id')
                            );
                    });
            });
        });
    }
}
