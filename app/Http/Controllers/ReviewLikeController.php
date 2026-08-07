<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewLike;


class ReviewLikeController extends Controller
{
    public function store(Review $review)
    {
        ReviewLike::firstOrCreate([
            'user_id' => auth()->id(),
            'review_id' => $review->id,
        ]);

        return back()
            ->with('success', 'レビューにいいねしました。');
    }


    public function destroy(Review $review)
    {
        ReviewLike::where('user_id', auth()->id())
            ->where('review_id', $review->id)
            ->delete();

        return back()
            ->with('success', 'レビューのいいねを解除しました。');
    }
}