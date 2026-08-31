<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'body',
        'image',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | User Relationship
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | One-to-Many: Post -> Comments
    |--------------------------------------------------------------------------
    */

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | One-to-Many: Post -> Top-Level Comments
    |--------------------------------------------------------------------------
    |
    | Only comments which are not replies.
    |
    */

    public function topLevelComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | One-to-Many: Post -> Likes
    |--------------------------------------------------------------------------
    */

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Search Scope
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        return $query->when($search, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )->orWhere(
                    'body',
                    'like',
                    "%{$search}%"
                );
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */

    public function scopeFilterByDate(
        Builder $query,
        ?string $from,
        ?string $to
    ): Builder {
        return $query
            ->when(
                $from,
                fn ($q, $from) =>
                    $q->whereDate(
                        'created_at',
                        '>=',
                        $from
                    )
            )
            ->when(
                $to,
                fn ($q, $to) =>
                    $q->whereDate(
                        'created_at',
                        '<=',
                        $to
                    )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | User Filter
    |--------------------------------------------------------------------------
    */

    public function scopeFilterByUser(
        Builder $query,
        ?int $userId
    ): Builder {
        return $query->when(
            $userId,
            fn ($q, $userId) =>
                $q->where(
                    'user_id',
                    $userId
                )
        );
    }
}