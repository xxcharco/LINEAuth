// resources/js/Pages/Partnership/Show.jsx
import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Alert, AlertDescription } from '@/Components/ui/alert';

const PartnershipShow = ({ auth, partnership, canInvite, flash }) => {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">パートナーシップ状態</h2>}
        >
            <Head title="パートナーシップ状態" />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {flash?.message && (
                                <Alert className="mb-4">
                                    <AlertDescription>{flash.message}</AlertDescription>
                                </Alert>
                            )}

                            {partnership ? (
                                <div className="space-y-4">
                                    <div className="border-b pb-4">
                                        <h3 className="text-lg font-medium mb-2">現在のパートナー</h3>
                                        <p className="text-gray-600">{partnership.partner.name}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">
                                            マッチング日時: {new Date(partnership.matched_at).toLocaleDateString()}
                                        </p>
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-600 mb-4">
                                        現在アクティブなパートナーシップはありません
                                    </p>
                                    {canInvite && (
                                        <a
                                            href="/partnerships/invite"
                                            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                        >
                                            パートナーを招待する
                                        </a>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default PartnershipShow;