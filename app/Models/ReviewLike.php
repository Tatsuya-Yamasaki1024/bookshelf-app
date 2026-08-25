<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'review_id',
    ];

    /**
     * いいねしたユーザーを取得する。
     *
     * @return BelongsTo ユーザーとの多対一リレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * いいねされたレビューを取得する。
     *
     * @return BelongsTo レビューとの多対一リレーション
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
