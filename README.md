# Creare un progetto

-   `composer create-project laravel/laravel nome_progetto`
-   `composer dump` (_ricrea gli indici di composer_)

# Starter Kits

-   [Breeze](https://laravel.com/docs/10.x/starter-kits)
-   [JetStream](https://jetstream.laravel.com/3.x/installation.html)

# Pacchetti utili di base

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

# Links rapidi documentazione

-   [Migration Tipi Colonne](https://laravel.com/docs/10.x/migrations#columns)
-   [Validatori predefiniti](https://laravel.com/docs/10.x/validation#available-validation-rules)
-   [Helpers](https://laravel.com/docs/10.x/helpers#available-methods)
-   [Collections](https://laravel.com/docs/10.x/collections#available-methods)
-   [Casts](https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting)
-   [Framework API](https://laravel.com/api/10.x/)

# Artisan commands

## Models and Migrations

```bash

# Make model with Factory and Migration
php artisan make:model Post -c -f -m

# Make Pivot Model with Migration
php artisan make:model Pivot/PostUser -m -p

# Make migration create
php artisan make:migration create_posts --create posts

# Make migration alter
php artisan make:migration alter_posts_add_notes --table posts

```

**Su SQLite per modificare le colonne, installare il pacchetto:**

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

php artisan db:seed
php artisan db:seed --class SampleSeeder

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

Same as "One to One"

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
$taxid->taxable // Collection<Person|Company|...>
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
$comment->commentable // Post|Video|null
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
