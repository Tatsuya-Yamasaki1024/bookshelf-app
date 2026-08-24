<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleBooksService
{
    /**
     * ISBNでGoogle Books APIから書籍情報を取得する。
     */
    public function searchByIsbn(string $isbn): ?array
    {
        $response = Http::get(
            'https://www.googleapis.com/books/v1/volumes',
            [
                'q' => 'isbn:'.$isbn,
            ]
        );

        // APIの利用上限超過
        if ($response->status() === 429) {
            throw new RuntimeException(
                'Google Books APIの利用上限に達しています。'
            );
        }

        // その他のAPIエラー
        if ($response->failed()) {
            throw new RuntimeException(
                'Google Books APIとの通信に失敗しました。'
            );
        }

        $data = $response->json();

        // 書籍が見つからない
        if (empty($data['items'])) {
            return null;
        }

        $volumeInfo = $data['items'][0]['volumeInfo'] ?? [];

        return [
            'title' => $volumeInfo['title'] ?? null,
            'author' => $volumeInfo['authors'][0] ?? null,
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'description' => $volumeInfo['description'] ?? null,
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'isbn' => $isbn,
        ];
    }
}
