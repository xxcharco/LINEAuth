<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnershipInvitationRequest extends FormRequest
{
    /**
     * リクエストの認可を判定
     */
    public function authorize(): bool
    {
        return true; // 認証済みユーザーのみがアクセスできるようルートで制限
    }

    /**
     * バリデーションルールを定義
     */
    public function rules(): array
    {
        return [
            'agreed_to_terms' => 'required|accepted',
        ];
    }

    /**
     * バリデーションメッセージをカスタマイズ
     */
    public function messages(): array
    {
        return [
            'agreed_to_terms.required' => '利用規約への同意が必要です',
            'agreed_to_terms.accepted' => '利用規約への同意が必要です',
        ];
    }
}