<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * ジャンルに紐付く書籍を取得する。
     *
     * @return BelongsToMany 書籍との多対多リレーション
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
