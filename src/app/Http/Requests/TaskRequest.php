<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:100'],
            'due_date' => ['required', 'date'],
            'is_done' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '作業内容を入力してください。',
            'title.max' => '作業内容は100文字以内で入力してください。',
            'due_date.required' => '期限日を入力してください。',
            'due_date.date' => '有効な日付形式で入力してください。',
            'is_done.boolean' => '状態の値が不正です。',
        ];
    }
}
