<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use App\Models\Review;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * マイ読書レポートを表示する。
     */
    public function index(): View
    {
        $userId = auth()->id();

        // 1. 基本サマリー

        // 総レビュー数
        $total_reviews = Review::where('user_id', $userId)->count();

        // 読了冊数（ユニーク書籍数）
        $books_read = ReadingPlan::where('user_id', $userId)
            ->where('status', 'completed')
            ->distinct('book_id')
            ->count('book_id');

        // 平均評価点
        $average_rating = Review::where('user_id', $userId)
            ->avg('rating') ?? 0;

        // 2. 評価分布: 1〜5星ごとの件数を横バーで表示
        $ratingCounts = Review::where('user_id', $userId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $rating_distribution = collect(range(1, 5))
            ->mapWithKeys(fn ($rating) => [
                $rating - 1 => $ratingCounts->get($rating, 0),
            ]);

        // 3. 高評価書籍TOP5: 4星以上の書籍を評価の高い順に最大5件表示
        $top_rated_books = Review::with('book')
            ->where('user_id', $userId)
            ->where('rating', '>=', 4)
            ->orderByDesc('rating')
            ->take(5)
            ->get()
            ->map(fn ($review) => [
                'id' => $review->book->id,
                'title' => $review->book->title,
                'author' => $review->book->author,
                'rating' => $review->rating,
            ]);

        // 4. ジャンル別評価傾向TOP5
        $genre_ratings = Review::with('book.genres')
            ->where('user_id', $userId)
            ->get()
            ->flatMap(function ($review) {
                return $review->book->genres->map(function ($genre) use ($review) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                        'rating' => $review->rating,
                    ];
                });
            })
            ->groupBy('id')
            ->map(function ($reviews) {
                return [
                    'id' => $reviews->first()['id'],
                    'name' => $reviews->first()['name'],
                    'count' => $reviews->count(),
                    'average_rating' => $reviews->avg('rating'),
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        // レポートデータをまとめる
        $stats = [
            'summary' => [
                'total_reviews' => $total_reviews,
                'books_read' => $books_read,
                'average_rating' => $average_rating,
            ],
            'rating_distribution' => $rating_distribution,
            'top_rated_books' => $top_rated_books,
            'genre_ratings' => $genre_ratings,
        ];

        return view('reports.index', compact('stats'));
    }
}
