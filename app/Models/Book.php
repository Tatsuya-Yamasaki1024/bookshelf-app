<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'isbn',
        'description',
        'image_url',
        'published_date',
    ];

    protected $casts = [
        'published_date' => 'date',
    ];

    /**
     * 書籍の登録ユーザーを取得する。
     *
     * @return BelongsTo 登録ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 書籍に紐付くジャンルを取得する。
     *
     * @return BelongsToMany ジャンルとの多対多リレーション
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * 書籍に投稿されたレビューを取得する。
     *
     * @return HasMany レビューとの一対多リレーション
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * 書籍のお気に入り情報を取得する。
     *
     * @return HasMany お気に入りとの一対多リレーション
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * 書籍をお気に入り登録しているユーザーを取得する。
     *
     * @return BelongsToMany ユーザーとの多対多リレーション
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * 書籍に紐付く読書計画を取得する。
     *
     * @return HasMany 読書計画との一対多リレーション
     */
    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }
}
