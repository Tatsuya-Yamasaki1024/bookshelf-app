<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $yamada = $users->firstWhere('name', '山田太郎');
        $suzuki = $users->firstWhere('name', '鈴木花子');

        // 山田太郎：主要シナリオ
        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books->get(0)->id,
            'target_date' => Carbon::today()->addDays(3),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books->get(1)->id,
            'target_date' => Carbon::today(),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books->get(2)->id,
            'target_date' => Carbon::today()->subDays(3),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books->get(3)->id,
            'target_date' => Carbon::today()->addDays(7),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books->get(4)->id,
            'target_date' => Carbon::today()->subDays(10),
            'status' => 'completed',
            'completed_at' => Carbon::today()->subDays(5),
        ]);

        // 鈴木花子：他ユーザー認可テスト用
        ReadingPlan::create([
            'user_id' => $suzuki->id,
            'book_id' => $books->get(5)->id,
            'target_date' => Carbon::today()->addDays(5),
            'status' => 'in_progress',
        ]);
    }
}
