import React from 'react';  // useState削除
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert } from '@/Components/ui/alert';

const PartnershipJoin = ({ auth, inviter, error, flash }) => {  
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
                                    LINE公式アカウントを友だち追加することでマッチングが完了します。
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default PartnershipJoin;