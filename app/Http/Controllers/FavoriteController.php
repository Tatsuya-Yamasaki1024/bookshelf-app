<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Favorite::with('book')
            ->where('user_id', auth()->id())
            ->paginate(10);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Book $book)
    {
        Favorite::firstOrCreate([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
        ]);

        return back()
            ->with('success', 'お気に入りに追加しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        Favorite::where('book_id', $book->id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()
            ->with('success', 'お気に入りを解除しました。');
    }
}
