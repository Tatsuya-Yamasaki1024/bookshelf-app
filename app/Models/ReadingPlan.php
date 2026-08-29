<?php

namespace App\Models;

use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'target_date',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
        'status' => ReadingPlanStatus::class,
    ];

    /**
     * 読書計画に紐付く書籍を取得する。
     *
     * @return BelongsTo 書籍との多対一リレーション
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * 読書計画を作成したユーザーを取得する。
     *
     * @return BelongsTo ユーザーとの多対一リレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
