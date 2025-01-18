<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenstruationEndRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.required' => '生理終了日を入力してください',
            'end_date.date' => '生理終了日は日付形式で入力してください',
        ];
    }
}