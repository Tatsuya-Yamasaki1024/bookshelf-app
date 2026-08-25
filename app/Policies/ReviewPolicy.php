<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * ユーザーがレビューを更新できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  Review  $review  更新対象のレビュー
     * @return bool 更新権限がある場合はtrue、ない場合はfalse
     */
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id;
    }

    /**
     * ユーザーがレビューを削除できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  Review  $review  削除対象のレビュー
     * @return bool 削除権限がある場合はtrue、ない場合はfalse
     */
    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id;
    }
}
