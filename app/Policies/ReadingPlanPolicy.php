<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;

class ReadingPlanPolicy
{
    /**
     * 読書計画を更新できるか判定する。
     *
     * @param User $user
     * @param ReadingPlan $readingPlan
     * @return bool
     */
    public function update(User $user, ReadingPlan $readingPlan): bool
    {
        return $user->id === $readingPlan->user_id;
    }

    /**
     * 読書計画を削除できるか判定する。
     *
     * @param User $user
     * @param ReadingPlan $readingPlan
     * @return bool
     */
    public function delete(User $user, ReadingPlan $readingPlan): bool
    {
        return $user->id === $readingPlan->user_id;
    }
}