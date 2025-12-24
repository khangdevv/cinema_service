# Eloquent ORM và Query Builder trong Laravel PHP

> Tài liệu tổng hợp kiến thức toàn diện về Eloquent ORM và Query Builder trong Laravel

---

## Mục lục

1. [Query Builder](#1-query-builder)
2. [Eloquent ORM](#2-eloquent-orm)
3. [Eloquent Relationships](#3-eloquent-relationships)
4. [Eloquent Events & Observers](#4-eloquent-events--observers)
5. [Collections](#5-collections)
6. [API Resources](#6-api-resources)
7. [Tips hay](#7-một-số-tips-hay)

---

## 1. Query Builder

Query Builder là một interface fluent để xây dựng và thực thi các câu truy vấn database. Nó sử dụng PDO parameter binding để bảo vệ ứng dụng khỏi SQL injection.

### 1.1. Truy vấn cơ bản

```php
use Illuminate\Support\Facades\DB;

// Lấy tất cả records
$users = DB::table('users')->get();

// Lấy một record đầu tiên
$user = DB::table('users')->first();

// Lấy một record theo ID
$user = DB::table('users')->find(1);

// Lấy giá trị của một column
$email = DB::table('users')->where('id', 1)->value('email');

// Lấy một column dưới dạng Collection
$emails = DB::table('users')->pluck('email');

// Lấy column với key tùy chỉnh
$emails = DB::table('users')->pluck('email', 'name');
```

### 1.2. Select cụ thể

```php
// Select các columns cụ thể
$users = DB::table('users')->select('name', 'email')->get();

// Select với alias
$users = DB::table('users')->select('name as user_name')->get();

// Thêm column vào select hiện tại
$query = DB::table('users')->select('name');
$users = $query->addSelect('email')->get();

// Select distinct
$users = DB::table('users')->distinct()->get();

// Raw expression
$users = DB::table('users')
    ->select(DB::raw('count(*) as user_count, status'))
    ->groupBy('status')
    ->get();
```

### 1.3. Where Clauses

```php
// Where cơ bản
$users = DB::table('users')->where('status', '=', 'active')->get();
$users = DB::table('users')->where('status', 'active')->get(); // Mặc định là '='

// Nhiều operators
$users = DB::table('users')->where('votes', '>=', 100)->get();
$users = DB::table('users')->where('votes', '<>', 100)->get();
$users = DB::table('users')->where('name', 'like', 'T%')->get();

// Mảng điều kiện
$users = DB::table('users')->where([
    ['status', '=', 'active'],
    ['subscribed', '<>', 1],
])->get();

// Or Where
$users = DB::table('users')
    ->where('votes', '>', 100)
    ->orWhere('name', 'John')
    ->get();

// Where Not
$users = DB::table('users')
    ->whereNot('status', 'banned')
    ->get();

// Where với Closure (grouping)
$users = DB::table('users')
    ->where('votes', '>', 100)
    ->orWhere(function ($query) {
        $query->where('name', 'Abigail')
              ->where('votes', '>', 50);
    })
    ->get();
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)

// whereBetween / whereNotBetween
$users = DB::table('users')
    ->whereBetween('votes', [1, 100])
    ->get();

// whereIn / whereNotIn
$users = DB::table('users')
    ->whereIn('id', [1, 2, 3])
    ->get();

// whereNull / whereNotNull
$users = DB::table('users')
    ->whereNull('updated_at')
    ->get();

// whereDate / whereMonth / whereDay / whereYear / whereTime
$users = DB::table('users')
    ->whereDate('created_at', '2023-12-01')
    ->get();

$users = DB::table('users')
    ->whereMonth('created_at', '12')
    ->get();

// whereColumn - So sánh 2 columns
$users = DB::table('users')
    ->whereColumn('first_name', 'last_name')
    ->get();

$users = DB::table('users')
    ->whereColumn('updated_at', '>', 'created_at')
    ->get();

// whereExists
$users = DB::table('users')
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('orders')
              ->whereColumn('orders.user_id', 'users.id');
    })
    ->get();

// JSON Where (cho database hỗ trợ JSON)
$users = DB::table('users')
    ->where('preferences->dining->meal', 'salad')
    ->get();
```

### 1.4. Ordering, Grouping, Limit & Offset

```php
// Order By
$users = DB::table('users')
    ->orderBy('name', 'desc')
    ->get();

// Latest / Oldest (theo created_at)
$users = DB::table('users')->latest()->first();
$users = DB::table('users')->oldest()->first();

// Random order
$user = DB::table('users')->inRandomOrder()->first();

// Remove existing orders
$query = DB::table('users')->orderBy('name');
$unorderedUsers = $query->reorder()->get();

// Group By / Having
$users = DB::table('users')
    ->groupBy('account_id')
    ->having('account_id', '>', 100)
    ->get();

// Limit & Offset
$users = DB::table('users')->skip(10)->take(5)->get();
// Hoặc
$users = DB::table('users')->offset(10)->limit(5)->get();
```

### 1.5. Joins

```php
// Inner Join
$users = DB::table('users')
    ->join('contacts', 'users.id', '=', 'contacts.user_id')
    ->join('orders', 'users.id', '=', 'orders.user_id')
    ->select('users.*', 'contacts.phone', 'orders.price')
    ->get();

// Left Join
$users = DB::table('users')
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->get();

// Right Join
$users = DB::table('users')
    ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
    ->get();

// Cross Join
$sizes = DB::table('sizes')
    ->crossJoin('colors')
    ->get();

// Advanced Join với Closure
$users = DB::table('users')
    ->join('contacts', function ($join) {
        $join->on('users.id', '=', 'contacts.user_id')
             ->orOn('users.id', '=', 'contacts.proxy_user_id');
    })
    ->get();

// Join với where
$users = DB::table('users')
    ->join('contacts', function ($join) {
        $join->on('users.id', '=', 'contacts.user_id')
             ->where('contacts.user_id', '>', 5);
    })
    ->get();

// Subquery Joins
$latestPosts = DB::table('posts')
    ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
    ->groupBy('user_id');

$users = DB::table('users')
    ->joinSub($latestPosts, 'latest_posts', function ($join) {
        $join->on('users.id', '=', 'latest_posts.user_id');
    })
    ->get();
```

### 1.6. Unions

```php
$first = DB::table('users')
    ->whereNull('first_name');

$users = DB::table('users')
    ->whereNull('last_name')
    ->union($first)
    ->get();
```

### 1.7. Aggregates

```php
$count = DB::table('users')->count();
$max = DB::table('orders')->max('price');
$min = DB::table('orders')->min('price');
$avg = DB::table('orders')->avg('price');
$sum = DB::table('orders')->sum('price');

// Kiểm tra record tồn tại
if (DB::table('orders')->where('finalized', 1)->exists()) {
    // ...
}

if (DB::table('orders')->where('finalized', 1)->doesntExist()) {
    // ...
}
```

### 1.8. Insert

```php
// Insert một record
DB::table('users')->insert([
    'email' => 'kayla@example.com',
    'votes' => 0
]);

// Insert nhiều records
DB::table('users')->insert([
    ['email' => 'picard@example.com', 'votes' => 0],
    ['email' => 'janeway@example.com', 'votes' => 0],
]);

// Insert và lấy ID
$id = DB::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);

// Upsert (Insert hoặc Update)
DB::table('flights')->upsert(
    [
        ['departure' => 'Oakland', 'destination' => 'San Diego', 'price' => 99],
        ['departure' => 'Chicago', 'destination' => 'New York', 'price' => 150]
    ],
    ['departure', 'destination'], // Unique columns
    ['price'] // Columns to update if exists
);

// Insert or Ignore
DB::table('users')->insertOrIgnore([
    ['id' => 1, 'email' => 'sushi@example.com'],
    ['id' => 2, 'email' => 'aria@example.com'],
]);
```

### 1.9. Update

```php
// Update cơ bản
$affected = DB::table('users')
    ->where('id', 1)
    ->update(['votes' => 1]);

// Update or Insert
DB::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'], // Điều kiện tìm
        ['votes' => 2] // Giá trị update/insert
    );

// Increment / Decrement
DB::table('users')->increment('votes');
DB::table('users')->increment('votes', 5);
DB::table('users')->decrement('votes');
DB::table('users')->decrement('votes', 5);

// Increment với update thêm columns khác
DB::table('users')->increment('votes', 1, ['name' => 'John']);
```

### 1.10. Delete

```php
// Delete với điều kiện
$deleted = DB::table('users')->where('votes', '>', 100)->delete();

// Delete tất cả
DB::table('users')->delete();

// Truncate (xóa tất cả và reset auto-increment)
DB::table('users')->truncate();
```

### 1.11. Pessimistic Locking

```php
// Shared lock
DB::table('users')
    ->where('votes', '>', 100)
    ->sharedLock()
    ->get();

// Lock for update
DB::table('users')
    ->where('votes', '>', 100)
    ->lockForUpdate()
    ->get();
```

### 1.12. Chunking Results (Xử lý data lớn)

```php
// Chunk - xử lý từng batch
DB::table('users')->orderBy('id')->chunk(100, function ($users) {
    foreach ($users as $user) {
        // ...
    }
    // Return false để dừng chunking
});

// ChunkById - an toàn hơn khi update trong chunk
DB::table('users')->where('active', false)
    ->chunkById(100, function ($users) {
        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['active' => true]);
        }
    });

// Lazy - stream results (sử dụng Generator)
DB::table('users')->orderBy('id')->lazy()->each(function ($user) {
    // ...
});

// LazyById
DB::table('users')->lazyById(200)->each(function ($user) {
    // ...
});
```

---

## 2. Eloquent ORM

Eloquent là Active Record ORM của Laravel, mỗi database table có một "Model" tương ứng để tương tác với table đó.

### 2.1. Định nghĩa Model

```php
// Tạo model với Artisan
// php artisan make:model User
// php artisan make:model User -m  (với migration)
// php artisan make:model User -mcf  (với migration, controller, factory)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // Tên table (mặc định là snake_case plural của class name)
    protected $table = 'users';
    
    // Primary key (mặc định là 'id')
    protected $primaryKey = 'id';
    
    // Primary key có auto-increment không
    public $incrementing = true;
    
    // Kiểu dữ liệu của primary key
    protected $keyType = 'int';
    
    // Có sử dụng timestamps không (created_at, updated_at)
    public $timestamps = true;
    
    // Format của timestamps
    protected $dateFormat = 'U';
    
    // Tên cột created_at và updated_at tùy chỉnh
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'updated_date';
    
    // Database connection
    protected $connection = 'mysql';
    
    // Giá trị mặc định cho attributes
    protected $attributes = [
        'options' => '[]',
        'delayed' => false,
    ];
}
```

### 2.2. Mass Assignment

```php
class User extends Model
{
    // Các fields được phép mass assign
    protected $fillable = ['name', 'email', 'password'];
    
    // Hoặc: Các fields KHÔNG được phép mass assign
    protected $guarded = ['id', 'is_admin'];
    
    // Cho phép tất cả (KHÔNG khuyến khích)
    protected $guarded = [];
}
```

### 2.3. Truy vấn với Eloquent

```php
use App\Models\User;

// Lấy tất cả
$users = User::all();

// Lấy với điều kiện
$users = User::where('active', 1)
    ->orderBy('name')
    ->take(10)
    ->get();

// Lấy một record
$user = User::find(1);
$user = User::where('email', 'john@example.com')->first();

// Find hoặc fail (throw ModelNotFoundException)
$user = User::findOrFail(1);
$user = User::where('email', 'john@example.com')->firstOrFail();

// Find hoặc tạo mới
$user = User::firstOrCreate(
    ['email' => 'john@example.com'],  // Điều kiện tìm
    ['name' => 'John']                 // Giá trị thêm nếu tạo mới
);

// First hoặc new (không tự động save)
$user = User::firstOrNew(
    ['email' => 'john@example.com'],
    ['name' => 'John']
);

// Update hoặc Create
$user = User::updateOrCreate(
    ['email' => 'john@example.com'],  // Điều kiện tìm
    ['name' => 'John', 'votes' => 1]  // Giá trị update/create
);
```

### 2.4. Insert với Eloquent

```php
// Cách 1: Tạo instance và save
$user = new User;
$user->name = 'John';
$user->email = 'john@example.com';
$user->save();

// Cách 2: Mass assignment với create
$user = User::create([
    'name' => 'John',
    'email' => 'john@example.com',
]);

// Insert nhiều records
User::insert([
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
]);
```

### 2.5. Update với Eloquent

```php
// Cách 1: Find và save
$user = User::find(1);
$user->name = 'New Name';
$user->save();

// Cách 2: Mass update
User::where('active', 1)
    ->update(['status' => 'verified']);

// Update với fill
$user = User::find(1);
$user->fill(['name' => 'New Name']);
$user->save();

// isDirty / isClean / wasChanged
$user = User::find(1);
$user->name = 'New Name';

$user->isDirty();           // true
$user->isDirty('name');     // true
$user->isDirty('email');    // false
$user->isClean();           // false

$user->save();

$user->wasChanged();        // true
$user->wasChanged('name');  // true
```

### 2.6. Delete với Eloquent

```php
// Cách 1: Find và delete
$user = User::find(1);
$user->delete();

// Cách 2: Delete trực tiếp
User::destroy(1);
User::destroy([1, 2, 3]);
User::destroy(collect([1, 2, 3]));

// Delete với điều kiện
User::where('active', 0)->delete();

// Truncate
User::truncate();
```

### 2.7. Soft Deletes

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;
    
    // Cột deleted_at sẽ được tự động quản lý
}

// Migration cần có
Schema::table('users', function (Blueprint $table) {
    $table->softDeletes(); // Thêm cột deleted_at
});

// Sử dụng
$user->delete(); // Soft delete

// Kiểm tra soft deleted
if ($user->trashed()) {
    // ...
}

// Bao gồm soft deleted records
$users = User::withTrashed()->get();

// Chỉ lấy soft deleted records
$users = User::onlyTrashed()->get();

// Restore
$user->restore();

// Force delete (xóa vĩnh viễn)
$user->forceDelete();
```

### 2.8. Attribute Casting

```php
class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
        'options' => 'array',
        'created_at' => 'datetime:Y-m-d',
        'birthday' => 'date',
        'secret' => 'encrypted',
        'preferences' => 'collection',
        'address' => AddressValueObject::class, // Custom cast
    ];
}

// Các cast types có sẵn:
// integer, real, float, double, decimal:<digits>
// string, boolean, object, array, collection
// date, datetime, immutable_date, immutable_datetime
// timestamp, encrypted, encrypted:array, encrypted:collection
// encrypted:object, hashed

// Custom Cast Class
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Json implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value, true);
    }

    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}
```

### 2.9. Accessors & Mutators (Laravel 9+)

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Model
{
    // Accessor - biến đổi khi đọc
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }
    
    // Mutator - biến đổi khi ghi
    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }
    
    // Kết hợp cả hai
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }
    
    // Virtual attribute (không có cột trong DB)
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }
    
    // Append virtual attributes to JSON/Array
    protected $appends = ['full_name'];
}
```

### 2.10. Scopes

```php
// Global Scope - áp dụng cho TẤT CẢ queries
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('active', 1);
    }
}

// Đăng ký Global Scope trong Model
class User extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);
        
        // Hoặc inline
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', 1);
        });
    }
}

// Bỏ qua Global Scope
User::withoutGlobalScope(ActiveScope::class)->get();
User::withoutGlobalScope('active')->get();
User::withoutGlobalScopes()->get();
User::withoutGlobalScopes([ActiveScope::class, AnotherScope::class])->get();

// Local Scope - gọi thủ công
class User extends Model
{
    // Scope phải bắt đầu bằng "scope"
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
    
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}

// Sử dụng Local Scope (không cần prefix "scope")
$users = User::active()->get();
$users = User::active()->ofType('admin')->get();
```

---

## 3. Eloquent Relationships

### 3.1. One to One

```php
// User có một Phone
class User extends Model
{
    public function phone()
    {
        return $this->hasOne(Phone::class);
        // Hoặc với custom keys
        return $this->hasOne(Phone::class, 'foreign_key', 'local_key');
    }
}

// Phone thuộc về User (inverse)
class Phone extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
        // Hoặc với custom keys
        return $this->belongsTo(User::class, 'foreign_key', 'owner_key');
    }
}

// Sử dụng
$phone = User::find(1)->phone;
$user = Phone::find(1)->user;
```

### 3.2. One to Many

```php
// User có nhiều Posts
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

// Post thuộc về User
class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Sử dụng
$posts = User::find(1)->posts;
foreach ($posts as $post) {
    // ...
}

// Query thêm
$posts = User::find(1)->posts()
    ->where('active', 1)
    ->orderBy('created_at', 'desc')
    ->get();
```

### 3.3. Many to Many

```php
// User có nhiều Roles, Role có nhiều Users
class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
        
        // Custom pivot table và keys
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
        
        // Với timestamps trên pivot
        return $this->belongsToMany(Role::class)->withTimestamps();
        
        // Với custom pivot columns
        return $this->belongsToMany(Role::class)->withPivot('active', 'created_by');
    }
}

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}

// Sử dụng
$roles = User::find(1)->roles;
foreach ($roles as $role) {
    echo $role->pivot->created_at; // Truy cập pivot data
}

// Attach / Detach / Sync
$user = User::find(1);

// Attach
$user->roles()->attach($roleId);
$user->roles()->attach($roleId, ['active' => 1]); // Với pivot data
$user->roles()->attach([1, 2, 3]); // Nhiều IDs

// Detach
$user->roles()->detach($roleId);
$user->roles()->detach([1, 2, 3]);
$user->roles()->detach(); // Detach tất cả

// Sync (chỉ giữ lại những IDs được specify)
$user->roles()->sync([1, 2, 3]);
$user->roles()->sync([1 => ['active' => 1], 2, 3]); // Với pivot data
$user->roles()->syncWithoutDetaching([1, 2, 3]);

// Toggle
$user->roles()->toggle([1, 2, 3]);

// Update pivot
$user->roles()->updateExistingPivot($roleId, ['active' => 0]);
```

### 3.4. Has Many Through

```php
// Country -> Users -> Posts
// Lấy tất cả Posts của một Country thông qua Users

class Country extends Model
{
    public function posts()
    {
        return $this->hasManyThrough(Post::class, User::class);
        
        // Custom keys
        return $this->hasManyThrough(
            Post::class,
            User::class,
            'country_id', // Foreign key on users table
            'user_id',    // Foreign key on posts table
            'id',         // Local key on countries table
            'id'          // Local key on users table
        );
    }
}
```

### 3.5. Has One Through

```php
// Mechanic -> Car -> Owner
class Mechanic extends Model
{
    public function carOwner()
    {
        return $this->hasOneThrough(Owner::class, Car::class);
    }
}
```

### 3.6. Polymorphic Relations

```php
// One to One Polymorphic
// Image có thể thuộc về User hoặc Post

// images table: id, url, imageable_id, imageable_type

class Image extends Model
{
    public function imageable()
    {
        return $this->morphTo();
    }
}

class Post extends Model
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

class User extends Model
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

// One to Many Polymorphic
// Comments có thể thuộc về Post hoặc Video

class Comment extends Model
{
    public function commentable()
    {
        return $this->morphTo();
    }
}

class Post extends Model
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

// Many to Many Polymorphic
// Tags có thể thuộc về Post hoặc Video

// taggables table: tag_id, taggable_id, taggable_type

class Tag extends Model
{
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}

class Post extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
```

### 3.7. Eager Loading

```php
// Giải quyết N+1 problem

// Lazy Loading (N+1 problem)
$users = User::all();
foreach ($users as $user) {
    echo $user->posts; // Query cho mỗi user
}

// Eager Loading
$users = User::with('posts')->get();
foreach ($users as $user) {
    echo $user->posts; // Không query thêm
}

// Eager Loading nhiều relationships
$users = User::with(['posts', 'roles'])->get();

// Nested Eager Loading
$users = User::with('posts.comments')->get();

// Eager Loading với constraints
$users = User::with(['posts' => function ($query) {
    $query->where('active', 1)
          ->orderBy('created_at', 'desc');
}])->get();

// Lazy Eager Loading (sau khi đã có collection)
$users = User::all();
$users->load('posts');
$users->load(['posts', 'roles']);

// Lazy Eager Loading với constraints
$users->load(['posts' => function ($query) {
    $query->where('active', 1);
}]);

// Load chỉ khi chưa load
$users->loadMissing('posts');

// Eager Loading Counts
$users = User::withCount('posts')->get();
echo $users[0]->posts_count;

// Với alias
$users = User::withCount(['posts as total_posts'])->get();

// Với conditions
$users = User::withCount(['posts' => function ($query) {
    $query->where('active', 1);
}])->get();
```

### 3.8. Saving Related Models

```php
// Save through relationship
$comment = new Comment(['message' => 'A new comment.']);
$post = Post::find(1);
$post->comments()->save($comment);

// Save nhiều
$post->comments()->saveMany([
    new Comment(['message' => 'First comment']),
    new Comment(['message' => 'Second comment']),
]);

// Create through relationship
$comment = $post->comments()->create([
    'message' => 'A new comment.',
]);

// Create nhiều
$post->comments()->createMany([
    ['message' => 'First comment'],
    ['message' => 'Second comment'],
]);

// Associate (belongsTo)
$account = Account::find(1);
$user->account()->associate($account);
$user->save();

// Dissociate
$user->account()->dissociate();
$user->save();
```

---

## 4. Eloquent Events & Observers

### 4.1. Model Events

```php
class User extends Model
{
    protected $dispatchesEvents = [
        'created' => UserCreated::class,
        'deleted' => UserDeleted::class,
    ];
    
    // Hoặc dùng closures
    protected static function booted()
    {
        static::creating(function ($user) {
            // Trước khi create
        });
        
        static::created(function ($user) {
            // Sau khi create
        });
        
        static::updating(function ($user) {
            // Trước khi update
        });
        
        static::updated(function ($user) {
            // Sau khi update
        });
        
        static::saving(function ($user) {
            // Trước khi save (create hoặc update)
        });
        
        static::saved(function ($user) {
            // Sau khi save
        });
        
        static::deleting(function ($user) {
            // Trước khi delete
        });
        
        static::deleted(function ($user) {
            // Sau khi delete
        });
        
        static::retrieved(function ($user) {
            // Sau khi retrieved từ DB
        });
        
        // Soft deletes
        static::restoring(function ($user) {});
        static::restored(function ($user) {});
        static::forceDeleted(function ($user) {});
    }
}
```

### 4.2. Observers

```php
// Tạo Observer
// php artisan make:observer UserObserver --model=User

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        // Before creating
    }
    
    public function created(User $user)
    {
        // After creating
    }
    
    public function updating(User $user)
    {
        // Before updating
    }
    
    public function updated(User $user)
    {
        // After updating
    }
    
    public function saving(User $user)
    {
        // Before saving
    }
    
    public function saved(User $user)
    {
        // After saving
    }
    
    public function deleting(User $user)
    {
        // Before deleting
    }
    
    public function deleted(User $user)
    {
        // After deleting
    }
    
    public function restored(User $user)
    {
        // After restoring (soft delete)
    }
    
    public function forceDeleted(User $user)
    {
        // After force deleting
    }
}

// Đăng ký Observer trong AppServiceProvider
use App\Models\User;
use App\Observers\UserObserver;

public function boot()
{
    User::observe(UserObserver::class);
}

// Hoặc dùng attribute (Laravel 10+)
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([UserObserver::class])]
class User extends Model
{
    // ...
}
```

---

## 5. Collections

Kết quả từ Eloquent được trả về dưới dạng `Illuminate\Database\Eloquent\Collection`.

```php
$users = User::where('active', 1)->get();

// Collection Methods
$users->count();
$users->first();
$users->last();
$users->isEmpty();
$users->isNotEmpty();

// Each
$users->each(function ($user) {
    echo $user->name;
});

// Map
$names = $users->map(function ($user) {
    return $user->name;
});

// Filter
$admins = $users->filter(function ($user) {
    return $user->is_admin;
});

// Reject (ngược với filter)
$nonAdmins = $users->reject(function ($user) {
    return $user->is_admin;
});

// Reduce
$total = $users->reduce(function ($carry, $user) {
    return $carry + $user->votes;
}, 0);

// Pluck
$names = $users->pluck('name');
$names = $users->pluck('name', 'id'); // Keyed by id

// GroupBy
$grouped = $users->groupBy('role');

// SortBy
$sorted = $users->sortBy('name');
$sorted = $users->sortByDesc('created_at');

// Contains
$users->contains('id', 1);
$users->contains(function ($user) {
    return $user->active;
});

// Sum / Avg / Min / Max
$totalVotes = $users->sum('votes');
$avgVotes = $users->avg('votes');

// Unique
$unique = $users->unique('email');

// Flatten
$flattened = $collection->flatten();

// Chunk
$chunks = $users->chunk(3);

// Take / Skip
$first3 = $users->take(3);
$skip2 = $users->skip(2);

// Where (collection)
$active = $users->where('active', true);
$active = $users->where('votes', '>', 100);

// FirstWhere
$user = $users->firstWhere('email', 'john@example.com');

// Diff / Intersect
$diff = $users->diff($anotherCollection);
$common = $users->intersect($anotherCollection);

// ToArray / ToJson
$array = $users->toArray();
$json = $users->toJson();

// Find trong collection
$user = $users->find(1);

// ModelKeys
$ids = $users->modelKeys(); // [1, 2, 3...]

// Fresh (reload từ DB)
$fresh = $users->fresh();
$fresh = $users->fresh('posts'); // Với relationship
```

---

## 6. API Resources

```php
// Tạo Resource
// php artisan make:resource UserResource
// php artisan make:resource UserCollection

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            
            // Conditional attributes
            'secret' => $this->when($request->user()->isAdmin(), 'secret-value'),
            
            // Nested resources
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            
            // Conditional relationship
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            
            // Meta data
            'links' => [
                'self' => route('users.show', $this->id),
            ],
        ];
    }
    
    // Additional meta data
    public function with($request)
    {
        return [
            'meta' => [
                'key' => 'value',
            ],
        ];
    }
}

// Sử dụng trong Controller
public function show(User $user)
{
    return new UserResource($user);
}

public function index()
{
    return UserResource::collection(User::all());
    
    // Hoặc với pagination
    return UserResource::collection(User::paginate());
}
```

---

## 7. Một số Tips hay

### 7.1. Debugging Queries

```php
// Xem SQL query
$query = User::where('active', 1);
dd($query->toSql()); // SELECT * FROM users WHERE active = ?
dd($query->getBindings()); // [1]

// Xem query với giá trị
User::where('active', 1)->dd();

// Log tất cả queries
DB::enableQueryLog();
// ... run queries ...
dd(DB::getQueryLog());

// Listen to queries
DB::listen(function ($query) {
    Log::info($query->sql);
    Log::info($query->bindings);
    Log::info($query->time);
});
```

### 7.2. Transactions

```php
use Illuminate\Support\Facades\DB;

// Automatic transaction
DB::transaction(function () {
    User::create(['name' => 'John']);
    Post::create(['title' => 'Hello']);
});

// Manual transaction
DB::beginTransaction();
try {
    User::create(['name' => 'John']);
    Post::create(['title' => 'Hello']);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### 7.3. Model Factories

```php
// database/factories/UserFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
    
    // States
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}

// Sử dụng
$user = User::factory()->create();
$users = User::factory()->count(10)->create();
$admin = User::factory()->admin()->create();

// Với relationships
$user = User::factory()
    ->has(Post::factory()->count(3))
    ->create();
```

---

## Tổng kết

| Thành phần | Mô tả |
|------------|-------|
| **Query Builder** | Interface fluent để xây dựng SQL queries trực tiếp |
| **Eloquent ORM** | Active Record ORM, mỗi table có một Model tương ứng |
| **Relationships** | Định nghĩa quan hệ giữa các Models |
| **Events** | Hooks vào lifecycle của Model |
| **Collections** | Wrapper mạnh mẽ cho arrays với nhiều helper methods |
| **API Resources** | Transform Models thành JSON responses |

---

> 📅 Cập nhật: Tháng 12/2024
> 
> 📚 Tài liệu tham khảo: [Laravel Documentation](https://laravel.com/docs)
