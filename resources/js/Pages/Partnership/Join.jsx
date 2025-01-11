// resources/js/Pages/Partnership/Join.jsx
import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert } from '@/components/ui/alert';

const PartnershipJoin = ({ auth, token, inviter, canAccept, isOwnInvitation, error, flash }) => {
    const [processing, setProcessing] = useState(false);

    const handleAccept = async () => {
        setProcessing(true);

        router.post(route('partnerships.match', token), {}, {
            onFinish: () => setProcessing(false)
        });
    };

    if (error) {
        return (
            <AuthenticatedLayout
                user={auth.user}
                header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">招待の確認</h2>}
            >
                <Head title="招待の確認" />

                <div className="py-12">
                    <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                        <Alert variant="destructive" className="mb-4">
                            {error}
                        </Alert>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">招待の確認</h2>}
        >
            <Head title="招待の確認" />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {flash.message && (
                                <Alert className="mb-4">
                                    {flash.message}
                                </Alert>
                            )}

                            {flash.error && (
                                <Alert variant="destructive" className="mb-4">
                                    {flash.error}
                                </Alert>
                            )}

                            <div className="mb-6">
                                <h3 className="text-lg font-medium mb-4">
                                    パートナーシップの招待
                                </h3>
                                <p className="text-gray-600">
                                    {inviter.name}さんからパートナーシップの招待が届いています。
                                </p>
                            </div>

                            {isOwnInvitation ? (
                                <Alert className="mb-4">
                                    これはあなたが作成した招待です
                                </Alert>
                            ) : !canAccept ? (
                                <Alert variant="destructive" className="mb-4">
                                    既にパートナーシップが存在するため、この招待を承認できません
                                </Alert>
                            ) : (
                                <div className="flex justify-end space-x-4">
                                    <a
                                        href={route('dashboard')}
                                        className="px-4 py-2 text-gray-600 hover:text-gray-800"
                                    >
                                        キャンセル
                                    </a>
                                    <button
                                        onClick={handleAccept}
                                        disabled={processing}
                                        className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {processing ? '処理中...' : '招待を承認する'}
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default PartnershipJoin;