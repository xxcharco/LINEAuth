import React from 'react';
import { Link } from '@inertiajs/react';

export default function Complete() {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <h1 className="text-center flex-1 font-bold">記録完了</h1>
                </div>

                {/* 完了メッセージ */}
                <div className="bg-white rounded-lg p-8 shadow-sm text-center">
                    <div className="mb-6">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-green-500 flex items-center justify-center">
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                className="h-8 w-8 text-white" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth={2} 
                                    d="M5 13l4 4L19 7" 
                                />
                            </svg>
                        </div>
                        <p className="text-lg">記録が完了しました</p>
                    </div>

                    {/* ボタングループ */}
                    <div className="space-y-4">
                        <Link
                            href={route('intimacy.index')}
                            className="block w-full bg-black text-white p-4 rounded-lg hover:bg-gray-800 transition"
                        >
                            続けて記録する
                        </Link>
                        
                        <Link
                            href={route('intimacy.list')}
                            className="block w-full bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition"
                        >
                            記録を確認する
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}