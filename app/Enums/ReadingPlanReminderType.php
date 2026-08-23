<?php

namespace App\Enums;

enum ReadingPlanReminderType: string
{
    case ThreeDaysBefore = 'three_days_before';
    case DueDate = 'due_date';
    case ThreeDaysAfter = 'three_days_after';
}
