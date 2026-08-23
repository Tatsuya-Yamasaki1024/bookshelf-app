<?php

namespace App\Notifications;

use App\Enums\ReadingPlanReminderType;
use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminder extends Notification
{
    use Queueable;

    /**
     * 読書計画リマインダー通知を生成する。
     */
    public function __construct(
        private ReadingPlan $readingPlan,
        private ReadingPlanReminderType $reminderType
    ) {}

    /**
     * 通知の配信チャンネルを取得する。
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * データベース通知の内容を取得する。
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'timing' => $this->reminderType->value,
            'title' => match ($this->reminderType) {
                ReadingPlanReminderType::ThreeDaysBefore => '読書計画の期限が近づいています。',
                ReadingPlanReminderType::DueDate => '読書計画の期限日です。',
                ReadingPlanReminderType::ThreeDaysAfter => '読書計画の期限を過ぎています。',
            },
            'body' => match ($this->reminderType) {
                ReadingPlanReminderType::ThreeDaysBefore => "「{$this->readingPlan->book->title}」の読書計画の期限が3日後です。期限日：{$this->readingPlan->target_date->format('Y-m-d')}",
                ReadingPlanReminderType::DueDate => "「{$this->readingPlan->book->title}」の読書計画の期限日です。期限日：{$this->readingPlan->target_date->format('Y-m-d')}",
                ReadingPlanReminderType::ThreeDaysAfter => "「{$this->readingPlan->book->title}」の読書計画は期限から3日経過しています。期限日：{$this->readingPlan->target_date->format('Y-m-d')}",
            },
            'reading_plan_id' => $this->readingPlan->id,
            'book_id' => $this->readingPlan->book_id,
        ];
    }
}
