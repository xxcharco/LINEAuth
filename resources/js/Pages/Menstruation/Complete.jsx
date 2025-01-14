import { Link } from '@inertiajs/react';

export default function Complete({ message = '記録しました' }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <h1 className="text-center flex-1 font-bold">tawagram</h1>
                </div>

                {/* 完了メッセージ */}
                <div className="bg-green-50 p-4 rounded-lg mb-4">
                    <div className="flex items-center text-green-600 mb-1">
                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{message}</span>
                    </div>
                </div>

                {/* アクションボタン */}
                <Link 
                    href="/menstruation" 
                    className="block w-full p-4 bg-black text-white rounded-lg text-center"
                >
                    閉じる
                </Link>
            </div>
        </div>
    );
}