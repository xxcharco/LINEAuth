<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenstruationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => '生理開始日を入力してください',
            'start_date.date' => '生理開始日は日付形式で入力してください',
            'end_date.date' => '生理終了日は日付形式で入力してください',
            'end_date.after_or_equal' => '生理終了日は開始日と同じか、それ以降の日付を指定してください',
        ];
    }
}