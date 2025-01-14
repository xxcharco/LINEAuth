<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenstruationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => '生理開始日を入力してください',
            'start_date.date' => '生理開始日は日付形式で入力してください',
        ];
    }
}