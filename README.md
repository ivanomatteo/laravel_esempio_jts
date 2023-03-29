# Create new project

-   `composer create-project laravel/laravel nome_progetto`
-   `composer dump` (_rebuild composer indexes_)

# Starter Kits

-   [Breeze](https://laravel.com/docs/starter-kits)
-   [JetStream](https://jetstream.laravel.com)

# Base Useful Packages

-   https://github.com/barryvdh/laravel-debugbar

    -   `composer require barryvdh/laravel-debugbar --dev`

-   https://github.com/barryvdh/laravel-ide-helper

    -   `composer require --dev barryvdh/laravel-ide-helper  `
    -   `php artisan ide-helper:generate  ` (base)
    -   `php artisan ide-helper:models -W  ` (models php-doc)
    -   `php artisan ide-helper:eloquent  ` (vendor-eloquent)

-   https://beyondco.de/docs/laravel-dump-server/installation

    -   `composer require --dev beyondcode/laravel-dump-server`
    -   `php artisan dump-server  ` (listen for dumps)

# Quick Docs Links

-   [Migration Columns Types](https://laravel.com/docs/migrations#columns)
-   [Builtin Validators](https://laravel.com/docs/validation#available-validation-rules)
-   [Helpers](https://laravel.com/docs/helpers#available-methods)
-   [Collections](https://laravel.com/docs/collections#available-methods)
-   [Casts](https://laravel.com/docs/eloquent-mutators#attribute-casting)
-   [Framework API](https://laravel.com/api/)
-   [Vue Js](https://vuejs.org/guide/introduction.html)
-   [Prime Vue](https://primevue.org/installation/)
-   [Inertia Js](https://inertiajs.com/)
-   [Tailwind Css](https://tailwindcss.com/docs/utility-first)
-   [Tailwind Snippets](https://tailwindcomponents.com/)

# Links PHP Features

-   [Php8.2](https://www.php.net/releases/8.2/en.php),
    [Php8.1](https://www.php.net/releases/8.1/en.php),
    [Php8.0](https://www.php.net/releases/8.0/en.php),
    [Php7.4](https://www.php.net/manual/en/migration74.new-features.php),
    [Php7.3](https://www.php.net/manual/en/migration73.new-features.php),
    [Php7.2](https://www.php.net/manual/en/migration72.new-features.php),
    [Php7.1](https://www.php.net/manual/en/migration71.new-features.php),
    [Php7.0](https://www.php.net/manual/en/migration70.new-features.php)

-   [Php Magic Methods](https://www.php.net/manual/en/language.oop5.magic.php)
-   [Php Magic Constants](https://www.php.net/manual/en/language.constants.magic.php)
-   [Php Watch Site](https://php.watch/versions)

# Artisan commands

## Basic

```bash

# Command list
php artisan list

# Command list by prefix
php artisan list make

# Command help
php artisan command:name --help

# Route list
php artisan route:list

# Route list in json format
php artisan route:list --json


```

## Models and Migrations

```bash

# Make model with Factory and Migration
php artisan make:model Post -f -m

# Make model with Factory,Migration and Resource Controller
php artisan make:model Post -f -m -c -r

# Make model with Factory,Migration and Api Resource Controller
php artisan make:model Post -f -m -c -r --api

# Make Pivot Model with Migration
php artisan make:model Pivot/PostUser -m -p

# Make migration create
php artisan make:migration create_posts --create posts

# Make migration alter
php artisan make:migration alter_posts_add_notes --table posts

```

**When using SQLite to alter columns install this package:**

```bash
composer require doctrine/dbal
```

## Controllers

```bash

# Make Controller
php artisan make:controller PostEmptyController

# Make Invocable Controller (sigle action controller)
php artisan make:controller PostArchive -i

# Make Controller Resource
php artisan make:controller PostController -r

# Make Controller API Resource
php artisan make:controller PostApiController -r --api

```

## Run Migrations and Seeders

```bash

php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
php artisan migrate:refresh --seed

# clear the database
php artisan db:wipe

php artisan db:seed
php artisan db:seed --class SampleSeeder

```

## Optimizations and Deploy

[Docs Deploy](https://laravel.com/docs/deployment)

```bash

#apply all optimizations
php artisan optimize

#clear all optimizations
php artisan optimize:clear


```

In case of problems:

```bash

# 1) manually delete cached files
rm bootstrap/cache/*.php

# 2) clear all optimizations
php artisan optimize:clear

# 3) clear application cache (with caution in production)
php artisan cache:clear

# 4) if in production: re-apply all optimizations
php artisan optimize

```

# Relations Cheat Sheets

## One to One

#### Models

```php

use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model {
    function userDetail(): HasOne{
        return $this->hasOne(UserDetail::class);
        //return $this->hasOne(UserDetail::class, foreignKey: 'user_id', localKey: 'id');
    }
}

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model {
    function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    //return $this->belongsTo(
    //  related: User::class,
    //  foreignKey: 'user_id', // deduced by this function name
    //  ownerKey: 'id'
    // );
```

#### Migrations

```php
Schema::create('user_details', function (Blueprint $table) {
    $table->foreignId('user_id')->unique();
    // add ->nullable() if make sense the related model exists without an owner
});
```

#### Usage

```php
// Store
$user->userDetail()->save($userDetail);
$userDetail->user()->associate($user)->save();

// Retrieve
$user->userDetail; // UserDetail|null
$userDetail->user; // User|null

```

## One to Many

#### Models

```php

use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model {
    function posts(): HasMany{
        return $this->hasMany(Post::class);
        //return $this->hasMany(Post::class, foreignKey: 'user_id', localKey: 'id');
    }
}

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model {
    function user(): BelongsTo{
        return $this->belongsTo(User::class);
        //return $this->belongsTo(
        //  related: User::class,
        //  foreignKey: 'user_id', // deduced by this function name
        //  ownerKey: 'id'
        // );
    }
}
```

#### Migrations

```php
Schema::create('posts', function (Blueprint $table) {
    $table->foreignId('user_id')->index();
});
```

#### Usage

```php
// Store
$user->posts()->save($post);
$user->posts()->saveMany([
    $post1,
    $post2,
]);
$post->user()->associate($user)->save();

// Retrieve
$user->posts; // Collection<Post> (empty Collection if none)
$post->user; // User|null

```

## One of Many

Consider "One to Many" configuration as starting point

#### Models

```php

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Database\Eloquent\Builder;

class User extends Model {

    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany();

        // alternatively you can convert an existing
        // OneToMany relation to OneToOne:
        // return $this->posts()->one()->latestOfMany();
    }

    public function oldestPost(): HasOne
    {
        return $this->hasOne(Post::class)->oldestOfMany();
    }

    public function mostViewedPost(): HasOne
    {
        return $this->hasOne(Post::class)->ofMany('views', 'max');
    }
    public function lastMonthMostViewedPost(): HasOne
    {
        return $this->hasOne(Price::class)->ofMany([
            'views' => 'max',
        ], function (Builder $query) {
            $query->where('published_at', '>=', now()->subMonth());
        });
    }
}

```

#### Usage

Same as "One to One" (but read only)

## Many to Many

#### Models

```php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model {
    function orders():BelongsToMany {
        return $this->belongsToMany(Order::class);
        //return $this->belongsToMany(
        //      related: Order::class,
        //      table: 'pivot_table', //default: Str::snake('Order') . '_' . Str::snake('Product') (in alphabetic order)
        //      foreignPivotKey: 'id',
        //      relatedPivotKey: 'order_id',
        //      parentKey: 'id',
        //      relatedKey: 'id' ,
        // )->withTimestamps() // optional use timestamps
        // ->withPivot('active', 'created_by'); // optional retrieve pivot columns
        // ->as('pivot_name_as') // Customizing The pivot Attribute Name
        // ->using(PivotModel::class); // optional use pivot model

    }
}

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model {
    function products():BelongsToMany {
        return $this->belongsToMany(Product::class);
    }
}
```

#### Migrations

```php

// Default table name (in alphabetic order):
// Str::snake('ModelOneName') . '_' . Str::snake('ModelTwoName')
// = model_one_name_model_two_name
Schema::create('order_product', function (Blueprint $table) {

    $table->id(); // optional, but can be useful

    $table->foreignId('order_id')->index()
        ->constrained()->cascadeOnDelete(); // optional, but recommended in most of cases

    $table->foreignId('product_id')->index()
        ->constrained()->cascadeOnDelete(); // optional, but recommended in most of cases

    $table->unique(['order_id', 'product_id']); // optional, but recommended
    //$table->primary(['order_id', 'product_id']); // if you don't use $table->id();

    $table->timestamps(); //needed when you use ->withTimestamps()
});
```

#### Usage

```php
// Store

$order->products()->attach($product1->id);
$order->products()->detach($product1->id);

// with pivot value
$order->products()->attach($product1->id, ['expires' => 3600]);

// multiple
$order->products()->attach([
    $product1->id,
    $product2->id,
]);
$order->products()->detach([
    $product1->id,
    $product2->id,
]);
$order->products()->sync([
    $product1->id,
    $product2->id,
]);

$order->products()->syncWithoutDetaching([$product1->id, $product2->id]);

// multiple with pivot values

$order->products()->attach([
    $product1->id => ['expires' => 3600],
    $product2->id => ['expires' => 300],
]);

$order->products()->sync([
        $product1->id => ['expires' => 3600],
        $product2->id,
    ]);

$order->products()->syncWithPivotValues([
        $product1->id,
        $product2->id,
    ], ['active' => true]);



// Retrieve
$order->products; // Collection<Product> (empty Collection if none)
$product->orders; // Collection<Order> (empty Collection if none)

```

## Polymorphic One to One

#### Models

```php

use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaxId extends Model {

    public function taxable(): MorphTo
    {
        return $this->morphTo();
    }
}

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Taxable {
    function taxId(): MorphOne {
        return $this->morphOne(TaxId::class,'taxable');
    }
}

class Person extends Model {
    use Taxable;
}

class Company extends Model {
    use Taxable;
}

```

#### Migrations

```php
Schema::create('tax_id', function (Blueprint $table) {

    $taxable = 'taxable'
    $this->string("{$taxable}_type");
    $this->foreignId("{$taxable}_id");
    $this->unique(["{$taxable}_type", "{$taxable}_id"]);

});
```

#### Usage

Same as non polymorphic "One to One"
Except:

```php
$taxid->taxable // Person|Company
```

## Polymorphic One to Many

#### Models

```php

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Commentable {
    function comments(): MorphMany {
        return $this->morphMany(Comment::class,'commentable');
    }
}

class Post extends Model {
    use Commentable;
}

class Video extends Model {
    use Commentable;
}

```

#### Migrations

```php
Schema::create('comments', function (Blueprint $table) {

    $table->morphs('commentable');
    // shortcut for:
    // $this->string("{$name}_type");
    // $this->unsignedBigInteger("{$name}_id");
    // $this->index(["{$name}_type", "{$name}_id"]);

});
```

#### Usage

Same as non polymorphic "One to Many"
Except:

```php
$comment->commentable // Post|Video
```

## Polymorphic Many to Many

#### Models

```php

use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model {

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }


    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable {
    function tags(): MorphToMany {
        return $this->morphToMany(Tag::class,'taggable');
    }
}

class Post extends Model {
    use Taggable;
}

class Video extends Model {
    use Taggable;
}

```

#### Migrations

```php
//default table name Str::plural('taggable')
Schema::create('taggables', function (Blueprint $table) {

    $table->foreignId('tag_id')->index();
        //->constrained()->cascadeOnDelete(); // optional

    $table->morphs('taggable');
    // shortcut for:
    // $this->string("{$name}_type");
    // $this->unsignedBigInteger("{$name}_id");
    // $this->index(["{$name}_type", "{$name}_id"]);

});
```

#### Usage

Same as non polymorphic "Many to Many"

# Relations Usage

## Querying Relations

Consider following relations:

-   User -> hasMany -> Post
-   Post -> hasMany -> Image
-   Post -> hasMany -> Comment

```php


$activePosts = $user->posts()->where('active', 1)->get();

/** Users that have at least 1 post
 * @var Illuminate\Database\Eloquent\Collection<User> $users */
$users = User::has('posts')->get();

/** Users that doesn't have posts
 * @var Illuminate\Database\Eloquent\Collection<User> $users */
$users = User::doesntHave('posts')->get();

/** Users that have at least 3 post
 * @var Illuminate\Database\Eloquent\Collection<User> $users */
$users = User::has('posts', '>=', 3)->get();

/** Nested relations query
 * Users that have at least 1 post that have at least 1 image
 * @var Illuminate\Database\Eloquent\Collection<User> $users */
$users = User::has('post.images')->get();

/** Custom query
 * @var Illuminate\Database\Eloquent\Collection<Post> $posts */
$posts = Post::whereHas('comments', function (Builder $query) {
    $query->where('content', 'like', 'code%');
})->get();

/** Custom query (absence)
 * @var Illuminate\Database\Eloquent\Collection<Post> $posts */
$posts = Post::whereDoesntHave('comments', function (Builder $query) {
    $query->where('content', 'like', 'code%');
})->get();


```

## Aggregating Related Models

```php

$post = Post::withCount('comments')->first();
echo $post->comments_count;

$user->loadCount('posts');
echo $user->posts_count;

$user = User::withSum('posts','votes');
echo $user->posts_sum_votes;

$posts = Post::withExists('comments');
echo $user->comments_exists;

```

## Eager loading and Pitfalls

```php

$user = User::take(10)->get();
$user->each(fn(User $u)=>dump($u->posts));
//this will cause the execution of 11 queries:
// 1x  "select * from users limit 10"
// 10x "select * from posts where user_id = ?"


$user = User::take(10)->with('posts')->get();
$user->each(fn(User $u)=>dump($u->posts));
//this will cause the execution of 2 queries only:
// 1x  "select * from users limit 10"
// 1x  "select * from posts where user_id in (?,?,?,?,?,?,?,?,?,?)"


$posts = $user->posts()->get();
$posts = $user->posts()->get();
$posts = $user->posts()->get();
//this will cause the execution of 3 queries:
// 3x "select * from posts where user_id = ?"


$posts = $user->posts;
$posts = $user->posts;
$posts = $user->posts;
//this will cause the execution of 1 queries only:
// 1x "select * from posts where user_id = ?"
// because $user->posts gets loaded the first time and 
// remain loaded in the $user instance


$filteredPosts = $user->posts->where('title','aaa');
// this will not cause a query,
// in this case we are invoking ->where() method
// of "Illuminate\Database\Eloquent\Collection"
// that returns a filtered Collection

```
