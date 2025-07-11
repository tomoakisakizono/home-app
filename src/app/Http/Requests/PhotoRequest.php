<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
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
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['file', 'mimetypes:image/heic,image/heif,image/jpeg,image/png,image/gif,image/webp', 'max:20480'],
            'photo_date' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => '写真を1枚以上選択してください。',
            'images.array' => '画像は配列で送信してください。',
            'images.max' => '写真は10枚以内で投稿してください。',
            'images.*.file' => 'ファイル形式が不正です。',
            'images.*.mimetypes' => 'JPEG/PNG/HEIC/GIF/WEBP画像のみアップロードできます。',
            'images.*.max' => '各画像サイズは20MB以内にしてください。',
            'photo_date.required' => '撮影日を入力してください。',
            'photo_date.date' => '正しい日付形式で入力してください。',
            'comment.max' => 'コメントは255文字以内で入力してください。',
            'category.required' => 'カテゴリを選択してください。',
        ];
    }
}
