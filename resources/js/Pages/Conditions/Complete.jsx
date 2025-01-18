import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import Footer from '@/Components/Footer';

export default function Complete({ message = '記録しました' }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー部分 */}
                <div className="flex items-center mb-6">
                    <Link href="/conditions" className="text-gray-400">
                        ←
                    </Link>
                    <h1 className="text-center flex-1 font-bold">tawagram</h1>
                    <button className="text-gray-400">×</button>
                </div>

                {/* 記録完了メッセージ */}
                <div className="bg-green-50 p-4 rounded-lg mb-4">
                    <div className="flex items-center text-green-600 mb-1">
                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>記録しました</span>
                    </div>
                    <p className="text-green-600 text-sm">
                        あなたの情報が正常に記録されました。
                    </p>
                </div>

                {/* メッセージカード */}
                <div className="bg-white rounded-lg p-4 mb-4 shadow-sm">
                    <h2 className="flex items-center text-gray-700 mb-2">
                        <span className="mr-2">💭</span>
                        あなたへのメッセージ
                    </h2>
                    <p className="text-gray-600 text-sm">
                        今日のプチアドバイス：深呼吸をして、自分の体調に耳を傾けましょう。
                    </p>
                </div>

                {/* アクションボタン */}
                <div className="space-y-2">
                    <Link 
                        href="/conditions/history" 
                        className="block w-full p-4 bg-white rounded-lg shadow-sm text-center"
                    >
                        昨日の体調を記録
                    </Link>
                    <Link 
                        href="/conditions/graph" 
                        className="block w-full p-4 bg-white rounded-lg shadow-sm text-center"
                    >
                        グラフを見る
                    </Link>
                    <Link 
                        href="/menstruation" 
                        className="block w-full p-4 bg-white rounded-lg shadow-sm text-center"
                    >
                        月経を記録
                    </Link>
                    <button 
                        className="block w-full p-4 bg-black text-white rounded-lg"
                    >
                        閉じる
                    </button>
                </div>
            </div>
            <Footer />
        </div>
    );
}