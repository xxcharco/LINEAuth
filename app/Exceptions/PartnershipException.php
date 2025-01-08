<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartnershipException extends Exception
{
    /**
     * 例外をJSONレスポンスまたはリダイレクトとしてレンダリング
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'message' => $this->getMessage(),
                'status' => 'error'
            ], 422);
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}

// 使用例：
throw new PartnershipException('パートナーシップの作成に失敗しました');
// または
throw new PartnershipException('既にアクティブなパートナーシップが存在します');