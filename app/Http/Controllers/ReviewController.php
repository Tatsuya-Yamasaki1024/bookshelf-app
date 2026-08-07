<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use App\Models\Book;


class ReviewController extends Controller
{
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


    public function edit(Review $review)
    {
        return view('reviews.edit', compact('review'));
    }

    public function update(ReviewRequest $request, Review $review)
    {
        $review->update($request->validated());

        return redirect()->route('books.show', $review->book)
            ->with('success', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        $book = $review->book;

        $review->delete();

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを削除しました。');
    }
}
