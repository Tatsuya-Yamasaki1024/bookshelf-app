<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * ユーザーが書籍を更新できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  Book  $book  更新対象の書籍
     * @return bool 更新権限がある場合はtrue、ない場合はfalse
     */
    public function update(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }

    /**
     * ユーザーが書籍を削除できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  Book  $book  削除対象の書籍
     * @return bool 削除権限がある場合はtrue、ない場合はfalse
     */
    public function delete(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }
}
