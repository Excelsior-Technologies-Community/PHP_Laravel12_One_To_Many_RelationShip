<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment',
        'post_id',
        'user_id',
        'parent_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Comment -> Post
    |--------------------------------------------------------------------------
    */

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Comment -> User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Comment -> Parent Comment
    |--------------------------------------------------------------------------
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Comment::class,
            'parent_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Comment -> Replies
    |--------------------------------------------------------------------------
    */

    public function replies(): HasMany
    {
        return $this->hasMany(
            Comment::class,
            'parent_id'
        );
    }
}