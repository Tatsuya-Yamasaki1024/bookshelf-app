<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['digits:13', 'unique:books,isbn'],
            'published_date' => ['date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => '登録者のユーザーIDを入力してください。',
            'user_id.exists' => '指定されたユーザーは存在しません。',

            'title.required' => 'タイトルを入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',

            'author.required' => '著者を入力してください。',
            'author.max' => '著者名は255文字以内で入力してください。',

            'isbn.digits' => '13桁のISBNを入力してください。',
            'isbn.unique' => 'そのISBNは既に登録されています。',

            'published_date.date' => '出版日は正しい日付を入力してください。',

            'description.max' => '説明は1000文字以内で入力してください。',

            'image_url.url' => '画像URLは正しいURL形式で入力してください。',
            'image_url.max' => '画像URLは1000文字以内で入力してください。',

            'genres.required' => 'ジャンルを1つ以上選択してください。',
            'genres.*.exists' => '指定されたジャンルは存在しません。',
        ];
    }
}
