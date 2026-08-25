<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReadingPlanRequest extends FormRequest
{
    /**
     * リクエストを実行できるか判定する。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前に現在の書籍IDを設定する。
     */
    protected function prepareForValidation(): void
    {
        $plan = $this->route('plan');

        $this->merge([
            'book_id' => $plan->book_id,
        ]);
    }

    /**
     * バリデーションルールを取得する。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $plan = $this->route('plan');

        return [
            'book_id' => [
                'required',
                'integer',
                Rule::unique('reading_plans', 'book_id')
                    ->where('user_id', auth()->id())
                    ->where('status', 'in_progress')
                    ->ignore($plan->id),
            ],
            'target_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ];
    }

    /**
     * バリデーションメッセージを取得する。
     */
    public function messages(): array
    {
        return [
            'book_id.unique' => 'この書籍には進行中の読書計画が既に存在します。',
            'target_date.required' => '期日を入力してください。',
            'target_date.date' => '期日は正しい日付を入力してください。',
            'target_date.after_or_equal' => '期日は今日以降の日付を指定してください。',
        ];
    }
}
