<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntimacyLogRequest extends FormRequest
{
    public function authorize()
    {
        return true; // ログインユーザーのみアクセス可能
    }

    public function rules()
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'type' => ['required', 'in:sex,masturbation'],
            'count' => ['nullable', 'integer', 'min:0'],
            'memo' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日付は必須です',
            'date.before_or_equal' => '未来の日付は選択できません',
            'count.min' => '回数は0以上で入力してください',
            'memo.max' => 'メモは500文字以内で入力してください',
        ];
    }
}