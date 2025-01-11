import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert, AlertDescription } from '@/Components/ui/alert';

const PartnershipConfirm = ({ auth, flash, errors }) => {
    const [agreed, setAgreed] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState(null);

    const handleInvitation = () => {
        // 1. 関数の実行確認
        console.log('handleInvitation関数が実行されました');
    
        if (!agreed) {
            console.log('利用規約に同意していません');
            return;
        }
        
        setProcessing(true);
        console.log('処理を開始します');
    
        // 2. POSTリクエストの送信
        router.post('/partnerships/invite', {
            agreed_to_terms: agreed
        }, {
            onSuccess: (response) => {
                // 3. 成功時の処理
                console.log('POSTリクエスト成功:', response);
                window.location.href = '/partnerships/invitation';
            },
            onError: (errors) => {
                // 4. エラー時の処理
                console.log('POSTリクエストエラー:', errors);
                setProcessing(false);
            },
            onFinish: () => {
                // 5. 完了時の処理
                console.log('処理が完了しました');
                setProcessing(false);
            }
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">パートナー招待</h2>}
        >
            <Head title="パートナー招待" />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {flash?.message && (
                                <Alert className="mb-4">
                                    <AlertDescription>{flash.message}</AlertDescription>
                                </Alert>
                            )}

                            {flash?.error && (
                                <Alert variant="destructive" className="mb-4">
                                    <AlertDescription>{flash.error}</AlertDescription>
                                </Alert>
                            )}

                            <div className="mb-6">
                                <h3 className="text-lg font-medium mb-4">パートナーシップについて</h3>
                                <div className="space-y-4 text-gray-600">
                                    <p>パートナーとの連携により、以下の機能が利用可能になります：</p>
                                    <ul className="list-disc pl-6 space-y-2">
                                        <li>共有データの閲覧と管理</li>
                                        <li>コミュニケーション機能の利用</li>
                                        <li>カップル向け特別機能へのアクセス</li>
                                    </ul>
                                </div>
                            </div>

                            <div className="mb-6 bg-gray-50 p-4 rounded">
                                <label className="flex items-start space-x-3">
                                    <input
                                        type="checkbox"
                                        className="mt-1"
                                        checked={agreed}
                                        onChange={(e) => setAgreed(e.target.checked)}
                                    />
                                    <span className="text-sm text-gray-600">
                                        私は利用規約とプライバシーポリシーに同意します
                                    </span>
                                </label>
                            </div>

                            <div className="flex justify-end space-x-4">
                                <a
                                    href="/dashboard"
                                    className="px-4 py-2 text-gray-600 hover:text-gray-800"
                                >
                                    キャンセル
                                </a>
                                <button
                                    onClick={handleInvitation}
                                    disabled={!agreed || processing}
                                    className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {processing ? '処理中...' : '招待する'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default PartnershipConfirm;