<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'content' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_title' => 'nullable|string|max:100',
            'event_description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'メッセージ内容を入力してください。',
            'content.max' => 'メッセージは255文字以内で入力してください。',
            'event_date.date' => '日付の形式が正しくありません。',
            'event_time.date_format' => '時間は HH:MM 形式で入力してください。',
            'event_title.max' => 'タイトルは100文字以内で入力してください。',
            'event_description.max' => '説明は255文字以内で入力してください。',
        ];
    }
}
