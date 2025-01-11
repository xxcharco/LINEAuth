// resources/js/Pages/Partnership/InvitationLink.jsx
import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert, AlertDescription } from '@/Components/ui/alert';

const InvitationLink = ({ auth, invitationUrl }) => {
    console.log('Received URL:', invitationUrl); // デバッグ用
    
    const [copied, setCopied] = useState(false);

    const copyToClipboard = () => {
        navigator.clipboard.writeText(invitationUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const shareToLine = () => {
        const lineShareUrl = `https://line.me/R/msg/text/?${encodeURIComponent(`パートナー招待が届いています\n招待の確認はこちら：${invitationUrl}`)}`;
        window.location.href = lineShareUrl;
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">招待リンク</h2>}
        >
            <Head title="招待リンク" />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-medium mb-4">パートナーを招待</h3>
                            
                            <div className="mb-6">
                                <p className="text-gray-600 mb-4">
                                    以下の方法でパートナーを招待できます：
                                </p>

                                {/* 招待リンク */}
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
                                            {copied ? 'コピーしました！' : 'リンクをコピー'}
                                        </button>
                                    </div>
                                </div>

                                {/* LINEで送る */}
                                <button
                                    onClick={shareToLine}
                                    className="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded flex items-center justify-center"
                                >
                                    <span>LINEで招待する友達を選択</span>
                                </button>
                            </div>

                            <div className="text-sm text-gray-500">
                                <p>※ 招待リンクの有効期限は7日間です</p>
                                <p>※ パートナーが登録を完了すると、LINEでお知らせします</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default InvitationLink;