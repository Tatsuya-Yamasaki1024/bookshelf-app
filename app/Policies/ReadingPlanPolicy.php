<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;

class ReadingPlanPolicy
{
    /**
     * ユーザーが読書計画を更新できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  ReadingPlan  $readingPlan  更新対象の読書計画
     * @return bool 更新権限がある場合はtrue、ない場合はfalse
     */
    public function update(User $user, ReadingPlan $readingPlan): bool
    {
        return $user->id === $readingPlan->user_id;
    }

    /**
     * ユーザーが読書計画を削除できるか判定する。
     *
     * @param  User  $user  認証ユーザー
     * @param  ReadingPlan  $readingPlan  削除対象の読書計画
     * @return bool 削除権限がある場合はtrue、ない場合はfalse
     */
    public function delete(User $user, ReadingPlan $readingPlan): bool
    {
        return $user->id === $readingPlan->user_id;
    }
}
