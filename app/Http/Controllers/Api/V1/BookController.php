<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\IndexBookResource;
use App\Http\Resources\StoreBookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得する。
     *
     * @param  IndexBookRequest  $request  書籍一覧取得リクエスト
     * @return IndexBookResource 書籍一覧のJSONリソース
     */
    public function index(IndexBookRequest $request): IndexBookResource
    {
        $query = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('genre_id')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        $perPage = $request->input('per_page', 20);
        $books = $query->paginate($perPage);

        return IndexBookResource::collection($books);
    }

    /**
     * 新しい書籍を登録する。
     *
     * 書籍本体の登録とジャンルの紐付けを1つのトランザクションで処理する。
     *
     * @param  StoreBookRequest  $request  書籍登録リクエスト
     * @return JsonResponse 登録した書籍のJSONレスポンス
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $book = DB::transaction(function () use ($validated) {
            $data = $validated;
            unset($data['genres']);

            $data['user_id'] = auth()->id();

            $book = Book::create($data);
            $book->genres()->attach($validated['genres']);

            return $book;
        });

        $book->load('genres');

        return (new StoreBookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 指定された書籍の詳細を取得する。
     *
     * @param  Book  $book  取得対象の書籍
     * @return BookResource 書籍詳細のJSONリソース
     */
    public function show(Book $book): BookResource
    {
        $book->load(['genres', 'reviews'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    /**
     * 指定された書籍を更新する。
     *
     * 書籍本体の更新とジャンルの紐付け更新を1つのトランザクションで処理する。
     *
     * @param  UpdateBookRequest  $request  書籍更新リクエスト
     * @param  Book  $book  更新対象の書籍
     * @return BookResource 更新した書籍のJSONリソース
     */
    public function update(
        UpdateBookRequest $request,
        Book $book
    ): BookResource {
        $this->authorize('update', $book);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $book) {
            $data = $validated;
            unset($data['genres']);

            $book->update($data);
            $book->genres()->sync($validated['genres']);
        });

        $book->load('genres');

        return new BookResource($book);
    }

    /**
     * 指定された書籍を削除する。
     *
     * @param  Book  $book  削除対象の書籍
     * @return JsonResponse 204 No Contentレスポンス
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(null, 204);
    }
}
