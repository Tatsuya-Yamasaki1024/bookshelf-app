<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * 書籍にレビューを投稿する。
     *
     * @return RedirectResponse
     */
    public function store(ReviewRequest $request, Book $book)
    {
        Review::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを投稿しました。');
    }

    /**
     * レビュー編集画面を表示する。
     */
    public function edit(Review $review): View
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューを更新する。
     */
    public function update(
        ReviewRequest $request,
        Review $review
    ): RedirectResponse {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return redirect()->route('books.show', $review->book)
            ->with('success', 'レビューを更新しました。');
    }

    /**
     * レビューを削除する。
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $book = $review->book;

        $review->delete();

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを削除しました。');
    }
}
