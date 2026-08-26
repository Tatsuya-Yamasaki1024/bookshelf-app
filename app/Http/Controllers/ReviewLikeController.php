<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewLike;
use Illuminate\Http\RedirectResponse;

class ReviewLikeController extends Controller
{
    /**
     * レビューへのいいね登録・解除を切り替える。
     *
     * @param  Review  $review  いいね対象のレビュー
     * @return RedirectResponse 元のページへのリダイレクト
     */
    public function toggle(Review $review): RedirectResponse
    {
        $like = ReviewLike::where('user_id', auth()->id())
            ->where('review_id', $review->id)
            ->first();

        if ($like) {
            $like->delete();

            return back()->with('success', 'いいねを取り消しました。');
        }

        ReviewLike::create([
            'user_id' => auth()->id(),
            'review_id' => $review->id,
        ]);

        return back()->with('success', 'レビューにいいねしました。');
    }
}
