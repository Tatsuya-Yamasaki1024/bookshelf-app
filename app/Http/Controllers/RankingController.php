<?php

namespace App\Http\Controllers;

use App\Models\Book;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rankingBooks = Book::whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return view('ranking.index', compact('rankingBooks'));
    }
}
