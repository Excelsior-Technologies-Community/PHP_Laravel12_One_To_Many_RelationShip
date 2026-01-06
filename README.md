# PHP_Laravel12_One_To_Many_RelationShip

# Step 1: Install Laravel 12 and Create project
```php
Composer create-project laravel/laravel your folder name “^12.0”
```
# Step 2 : Setup Database for.env file
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your database name
DB_USERNAME=root
DB_PASSWORD=
```
# Step 3: Create posts table for migration file
```php
php artisan make:migration create_posts_table
```
```php
<?php
  
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
  
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });
    }
  
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```
# Step 4: Create comments table for migration file
```php
php artisan make:migration create_comments_table
```
```php
<?php
  
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
  
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts');
            $table->string("comment");
            $table->timestamps();
        });
    }
  
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
```
# Step 5 : Run Migration
```php
php artisan migrate
```

# Step 6: Models Create 
```php
php artisan make:model Post
php artisan make:model Comment
```

# Post Model
```php
 app/Models/Post.php
```
```php
<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
  
class Post extends Model
{
    use HasFactory;
 
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}

```
# Comment Model
```php
 app/Models/Comment.php
```
```php
<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
  
class Comment extends Model
{
    use HasFactory;
  
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
```
# Step 7: Create PostController
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class PostController extends Controller
{
    public function index()
    {
        //  Post ID 1 fetch karo
        $post = Post::find(1);

        //  Safety check (important)
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found. Please insert post first.'
            ]);
        }

        //  Comment 1
        $comment1 = new Comment();
        $comment1->comment = "Hi Comment 1";

        //  Comment 2
        $comment2 = new Comment();
        $comment2->comment = "Hi Comment 2";

        //  Save multiple comments
        $post->comments()->saveMany([$comment1, $comment2]);

        return response()->json([
            'status' => true,
            'message' => 'Comments saved successfully',
            'post_id' => $post->id
        ]);
    }
}
```
# Step 8: Create web route for routes/web.php file
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/test', [PostController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
```
 # Step 9:Now Run Server and paste this url
 ```php
php artisan serve
```
```php
http://127.0.0.1:8000/test
```
 <img width="1349" height="214" alt="image" src="https://github.com/user-attachments/assets/13ccbd73-d338-4c5a-a1bd-b4e65fc74130" />

# Now Open your comments and posts table for database
 
 <img width="1203" height="394" alt="image" src="https://github.com/user-attachments/assets/f11dd4a9-2488-4987-92dc-bf7f7911c8f2" />
<img width="1363" height="587" alt="image" src="https://github.com/user-attachments/assets/e4d1fbed-7ed8-4241-abb7-70408b7a3e2a" />




 





