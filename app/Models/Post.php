<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'body',
        'image',
        'user_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Top Level Comments
    |--------------------------------------------------------------------------
    */

    public function topLevelComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Bookmarks
    |--------------------------------------------------------------------------
    */

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    */

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        Builder $query,
        string $search
    ): Builder {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */

    public function scopeFilterByDate(
        Builder $query,
        $from,
        $to = null
    ): Builder {
        if ($from) {
            $query->whereDate(
                'created_at',
                '>=',
                $from
            );
        }

        if ($to) {
            $query->whereDate(
                'created_at',
                '<=',
                $to
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | User Filter
    |--------------------------------------------------------------------------
    */

    public function scopeFilterByUser(
        Builder $query,
        $userId
    ): Builder {
        return $query->where(
            'user_id',
            $userId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Published Scope
    |--------------------------------------------------------------------------
    */

    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'published'
        );
    }
}