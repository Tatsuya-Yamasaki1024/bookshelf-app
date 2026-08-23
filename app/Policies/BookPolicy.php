<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * 書籍の更新権限を判定する。
     */
    public function update(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }

    /**
     * 書籍の削除権限を判定する。
     */
    public function delete(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }
}
