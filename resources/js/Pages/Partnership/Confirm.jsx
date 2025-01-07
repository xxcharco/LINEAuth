import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { Alert, AlertDescription } from '@/components/ui/alert';

const PartnershipConfirm = ({ auth, flash }) => {
    const [agreed, setAgreed] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState(null);

    const handleInvitation = async () => {
        if (!agreed) return;
        
        setProcessing(true);
        setError(null);

        try {
            await router.post('/partnerships/invite', {}, {
                onSuccess: () => {
                    // リダイレクトはInertiaが自動的に処理
                },
                onError: (errors) => {
                    setError(errors.message || '招待の作成に失敗しました');
                    setProcessing(false);
                }
            });
        } catch (e) {
            setError('予期せぬエラーが発生しました');
            setProcessing(false);
        }
    };

    return (
        <div className="max-w-2xl mx-auto p-6">
            <h1 className="text-2xl font-bold mb-6">パートナー招待</h1>

            {error && (
                <Alert variant="destructive" className="mb-4">
                    <AlertDescription>{error}</AlertDescription>
                </Alert>
            )}

            {flash.message && (
                <Alert className="mb-4">
                    <AlertDescription>{flash.message}</AlertDescription>
                </Alert>
            )}

            <div className="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 className="text-xl font-semibold mb-4">パートナーシップについて</h2>
                <div className="space-y-4 text-gray-600">
                    <p>パートナーとの連携により、以下の機能が利用可能になります：</p>
                    <ul className="list-disc pl-6 space-y-2">
                        <li>共有データの閲覧と管理</li>
                        <li>コミュニケーション機能の利用</li>
                        <li>カップル向け特別機能へのアクセス</li>
                    </ul>
                </div>
            </div>

            <div className="bg-gray-50 p-4 rounded-lg mb-6">
                <label className="flex items-start space-x-3">
                    <input
                        type="checkbox"
                        className="mt-1"
                        checked={agreed}
                        onChange={(e) => setAgreed(e.target.checked)}
                    />
                    <span className="text-sm text-gray-600">
                        私は<Link href="/terms" className="text-blue-600 hover:underline">利用規約</Link>
                        および<Link href="/privacy" className="text-blue-600 hover:underline">プライバシーポリシー</Link>
                        に同意します
                    </span>
                </label>
            </div>

            <div className="flex justify-end space-x-4">
                <Link
                    href="/dashboard"
                    className="px-4 py-2 text-gray-600 hover:text-gray-800"
                >
                    キャンセル
                </Link>
                <button
                    onClick={handleInvitation}
                    disabled={!agreed || processing}
                    className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {processing ? '処理中...' : '招待する'}
                </button>
            </div>
        </div>
    );
};

export default PartnershipConfirm;