<?php

namespace App\Http\Controllers;

use App\Http\Requests\IsbnSearchRequest;
use App\Services\GoogleBooksService;
use Illuminate\Http\JsonResponse;

class BookIsbnController extends Controller
{
    public function __construct(
        private GoogleBooksService $googleBooksService
    ) {}

    /**
     * ISBNで書籍情報を検索する。
     *
     * @param  IsbnSearchRequest  $request  ISBN検索リクエスト
     * @return JsonResponse Google Books APIから取得した書籍情報のJSONレスポンス
     */
    public function searchByIsbn(IsbnSearchRequest $request): JsonResponse
    {
        $isbn = $request->validated('isbn');

        try {
            $book = $this->googleBooksService->searchByIsbn($isbn);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 503);
        }

        if ($book === null) {
            return response()->json([
                'error' => '書籍情報が見つかりませんでした。',
            ], 404);
        }

        return response()->json($book);
    }
}
