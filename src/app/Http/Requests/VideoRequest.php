<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'youtube_url' => ['required', 'url'],
            'comment' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'registered_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'youtube_url.required' => 'YouTubeのURLを入力してください。',
            'youtube_url.url' => '有効なURL形式で入力してください。',
            'comment.max' => 'コメントは255文字以内で入力してください。',
            'category.required' => 'カテゴリを選択してください。',
            'registered_at.required' => '登録日を入力してください。',
            'registered_at.date' => '日付の形式が正しくありません。',
        ];
    }
}
