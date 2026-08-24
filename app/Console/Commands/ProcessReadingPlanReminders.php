<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanReminderType;
use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminder;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;

class ProcessReadingPlanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reading-plans:process-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '読書計画の期限状態を更新し、リマインダー通知を送信する。';

    /**
     * 読書計画の期限状態を更新し、リマインダー通知を送信する。
     */
    public function handle(): int
    {
        $today = today();

        // 期限を経過した進行中の計画を期限切れにする。
        ReadingPlan::where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', '<', $today)
            ->update([
                'status' => ReadingPlanStatus::Expired,
            ]);

        // 期限3日前の通知
        $this->sendReminders(
            $today->copy()->addDays(3),
            ReadingPlanStatus::InProgress,
            ReadingPlanReminderType::ThreeDaysBefore
        );

        // 期限当日の通知
        $this->sendReminders(
            $today,
            ReadingPlanStatus::InProgress,
            ReadingPlanReminderType::DueDate
        );

        // 期限切れから3日後の通知
        $this->sendReminders(
            $today->copy()->subDays(3),
            ReadingPlanStatus::Expired,
            ReadingPlanReminderType::ThreeDaysAfter
        );

        $this->info('読書計画のリマインダー処理が完了しました。');

        return self::SUCCESS;
    }

    /**
     * 条件に一致する読書計画へリマインダー通知を送信する。
     */
    private function sendReminders(
        CarbonInterface $targetDate,
        ReadingPlanStatus $status,
        ReadingPlanReminderType $reminderType
    ): void {
        ReadingPlan::with('book', 'user')
            ->where('status', $status)
            ->whereDate('target_date', $targetDate)
            ->get()
            ->each(function (ReadingPlan $readingPlan) use ($reminderType) {
                $alreadySent = $readingPlan->user
                    ->notifications()
                    ->where('type', ReadingPlanReminder::class)
                    ->whereDate('created_at', today())
                    ->where('data->timing', $reminderType->value)
                    ->where('data->reading_plan_id', $readingPlan->id)
                    ->exists();

                if ($alreadySent) {
                    return;
                }

                $readingPlan->user->notify(
                    new ReadingPlanReminder(
                        $readingPlan,
                        $reminderType
                    )
                );
            });
    }
}
