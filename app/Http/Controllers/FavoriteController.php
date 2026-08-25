<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * ログインユーザーのお気に入り書籍一覧を表示する。
     *
     * @return View お気に入り書籍一覧画面
     */
    public function index(): View
    {
        $books = auth()->user()
            ->favoriteBooks()
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * 書籍のお気に入り登録・解除を切り替える。
     *
     * @param  Book  $book  お気に入り登録・解除の対象となる書籍
     * @return RedirectResponse 元のページへのリダイレクト
     */
    public function toggle(Book $book): RedirectResponse
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'お気に入りから削除しました。');
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
        ]);

        return back()->with('success', 'お気に入りに追加しました。');
    }
}
