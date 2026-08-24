<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧を表示する。
     */
    public function index(Request $request): View
    {
        $genres = Genre::all();

        $keyword = $request->input('keyword');
        $genre = $request->input('genre');
        $sort = $request->input('sort', 'newest');

        $books = Book::with('genres')
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('author', 'like', "%{$keyword}%");
                });
            })
            ->when($genre, function ($query, $genre) {
                $query->whereHas('genres', function ($query) use ($genre) {
                    $query->where('genres.id', $genre);
                });
            })
            ->when($sort === 'newest', function ($query) {
                $query->orderByDesc('created_at');
            })
            ->when($sort === 'oldest', function ($query) {
                $query->orderBy('created_at');
            })
            ->when($sort === 'title', function ($query) {
                $query->orderBy('title');
            })
            ->when($sort === 'rating', function ($query) {
                $query->withAvg('reviews', 'rating')
                    ->orderByRaw('reviews_avg_rating IS NULL')
                    ->orderByDesc('reviews_avg_rating');
            })
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact(
            'books',
            'genres',
            'keyword',
            'genre',
            'sort'
        ));
    }

    /**
     * 書籍登録画面を表示する。
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 新しい書籍を登録する。
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $validated['user_id'] = auth()->id();

        $book = Book::create($validated);
        $book->genres()->attach($genreIds);

        return redirect()->route('books.index')
            ->with('success', '書籍を登録しました。');
    }

    /**
     * 書籍詳細を表示する。
     */
    public function show(Book $book): View
    {
        $book->load(['genres', 'reviews.user']);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍編集画面を表示する。
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);
        $book->load('genres');
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍を更新する。
     */
    public function update(
        UpdateBookRequest $request,
        Book $book
    ): RedirectResponse {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $book->update($validated);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を更新しました。');
    }

    /**
     * 書籍を削除する。
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', '書籍を削除しました。');
    }
}
