<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IsbnSearchRequest extends FormRequest
{
    /**
     * リクエストを実行できるか判定する。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前にISBNを設定する。
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => $this->route('isbn'),
        ]);
    }

    /**
     * バリデーションルールを取得する。
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'isbn' => ['required', 'digits:13'],
        ];
    }

    /**
     * バリデーションメッセージを取得する。
     */
    public function messages(): array
    {
        return [
            'isbn.required' => 'ISBNを入力してください。',
            'isbn.digits' => 'ISBNは13桁で入dsds力してください。',
        ];
    }
}
