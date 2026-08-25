<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    /**
     * レビューを投稿したユーザーを取得する。
     *
     * @return BelongsTo ユーザーとの多対一リレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * レビュー対象の書籍を取得する。
     *
     * @return BelongsTo 書籍との多対一リレーション
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * レビューに付けられたいいねを取得する。
     *
     * @return HasMany レビューいいねとの一対多リレーション
     */
    public function reviewLikes(): HasMany
    {
        return $this->hasMany(ReviewLike::class);
    }

    /**
     * レビューにいいねしたユーザーを取得する。
     *
     * @return BelongsToMany ユーザーとの多対多リレーション
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_likes');
    }
}
