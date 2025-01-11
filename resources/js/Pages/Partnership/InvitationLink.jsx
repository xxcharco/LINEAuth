import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert, AlertDescription } from '@/Components/ui/alert';

const InvitationLink = ({ auth, invitationUrl }) => {

    // デバッグ用のログ
    console.log('Component rendered with URL:', invitationUrl);

    useEffect(() => {
        console.log('Received props:', {
            invitationUrl,
            auth
        });
    }, [invitationUrl, auth]);

    const [copied, setCopied] = useState(false);

    

    const copyToClipboard = () => {
        navigator.clipboard.writeText(invitationUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const shareToLine = () => {
        const lineShareUrl = `https://line.me/R/msg/text/?${encodeURIComponent(`パートナーシップの招待が届いています\n以下のURLから公式LINEを友だち追加してください：\n${invitationUrl}`)}`;
        window.location.href = lineShareUrl;
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
                            <h3 className="text-lg font-medium mb-4">パートナーを招待</h3>
                            
                            <div className="mb-6">
                                <p className="text-gray-600 mb-4">
                                    以下のURLをパートナーに共有してください。
                                    パートナーが公式LINEを友だち追加することで、マッチングが完了します。
                                </p>

                                {/* 友だち追加URL */}
                                <div className="bg-gray-50 p-4 rounded mb-4">
                                    <div className="flex items-center justify-between">
                                        <div className="overflow-hidden">
                                            <p className="text-sm text-gray-500 truncate">
                                                {invitationUrl}
                                            </p>
                                        </div>
                                        <button
                                            onClick={copyToClipboard}
                                            className="ml-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded"
                                        >
                                            {copied ? 'コピーしました！' : 'URLをコピー'}
                                        </button>
                                    </div>
                                </div>

                                {/* LINEで送る */}
                                <button
                                    onClick={shareToLine}
                                    className="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded flex items-center justify-center"
                                >
                                    <span>LINEでURLを共有する</span>
                                </button>
                            </div>

                            <div className="text-sm text-gray-500">
                                <p>※ パートナーが公式LINEを友だち追加すると、マッチング完了のお知らせがLINEで届きます</p>
                                <p>※ マッチングが完了するまでは、URLを共有したパートナーのみがあなたとマッチングできます</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default InvitationLink;