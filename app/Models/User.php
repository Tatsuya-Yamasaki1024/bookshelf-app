<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ユーザーが登録した書籍を取得する。
     *
     * @return HasMany 書籍との一対多リレーション
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * ユーザーが投稿したレビューを取得する。
     *
     * @return HasMany レビューとの一対多リレーション
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * ユーザーのお気に入り情報を取得する。
     *
     * @return HasMany お気に入りとの一対多リレーション
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * ユーザーがお気に入り登録した書籍を取得する。
     *
     * @return BelongsToMany 書籍との多対多リレーション
     */
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites');
    }

    /**
     * ユーザーが付けたレビューいいねを取得する。
     *
     * @return HasMany レビューいいねとの一対多リレーション
     */
    public function reviewLikes(): HasMany
    {
        return $this->hasMany(ReviewLike::class);
    }

    /**
     * ユーザーがいいねしたレビューを取得する。
     *
     * @return BelongsToMany レビューとの多対多リレーション
     */
    public function likedReviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes');
    }

    /**
     * ユーザーの読書計画を取得する。
     *
     * @return HasMany 読書計画との一対多リレーション
     */
    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }
}
