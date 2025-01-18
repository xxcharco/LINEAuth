import { Link } from '@inertiajs/react';
import { useState } from 'react'; 

export default function Index({ records }) {
    const [activeTab, setActiveTab] = useState(2);
    const currentRecord = records[2 - activeTab] || null;

    // 最新の記録を取得（タブ選択に関係なく常に最新）
    const latestRecord = records[0] || null;

    // ボタンのテキストとリンク先を決定する関数
    const getRecordButton = () => {
        console.log('Latest Record:', latestRecord);
    
        // 記録が全くない場合、または最新の記録に終了日が記録済みの場合
        if (!latestRecord || latestRecord.end_date) {
            return {
                text: "生理始まったかも？",
                href: route('menstruation.create')  // 生理開始日記録用ルート
            };
        }
    
        // 最新の記録の生理終了日が未記録の場合
        return {
            text: "生理終わったかも？",
            href: route('menstruation.create', { type: 'end' })  // 生理終了日記録用のパラメータを追加
        };
    };

    const recordButton = getRecordButton();

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                <h1 className="text-center text-xl font-bold mb-8">月経管理</h1>
                
                {/* 生理記録セクション */}
                <div className="bg-white rounded-lg p-4 shadow-sm mb-6">
                    <h2 className="text-lg font-medium mb-4">最近の生理状況は？</h2>
                    <Link 
                        href={recordButton.href}
                        className="block w-full bg-gray-700 text-white p-4 rounded-lg mb-4 text-center"
                    >
                        {recordButton.text}
                    </Link>
                    
                {/* タブ切り替え */}
                <div className="flex mb-4">
                    {['2回前', '1回前', '最近'].map((label, index) => (
                        <button
                            key={label}
                            onClick={() => setActiveTab(index)}
                            className={`flex-1 py-2 ${
                                activeTab === index 
                                ? 'border-b-2 border-black font-medium' 
                                : 'text-gray-500'
                            }`}
                        >
                            {label}
                        </button>
                    ))}
                </div>

                {/* 記録の表示 */}
                <div className="space-y-4">
                    <div>
                        <div className="flex items-center mb-1">
                            <span className="text-yellow-500 mr-2">●</span>
                            <span>生理開始日</span>
                        </div>
                        <div className="text-center py-2 bg-gray-50 rounded">
                            {currentRecord?.start_date || '—記録なし—'}
                        </div>
                    </div>
                    
                    <div>
                        <div className="flex items-center mb-1">
                            <span className="text-yellow-500 mr-2">●</span>
                            <span>生理終了日</span>
                        </div>
                        <div className="text-center py-2 bg-gray-50 rounded">
                            {currentRecord?.end_date || '—記録なし—'}
                        </div>
                    </div>
                    {/* 編集ボタンを追加 */}
                    {currentRecord && (
                    <Link
                        href={route('menstruation.edit', { menstruation: currentRecord.id })}
                        className="block w-full text-center py-2 text-gray-600 border border-gray-300 rounded"
                    >
                        生理日を編集する
                    </Link>
                    )}
                </div>
                </div>
                {/* なかよしログセクション */}
                <div className="bg-white rounded-lg p-4 shadow-sm">
                    <h2 className="text-lg font-medium mb-2">なかよしログ</h2>
                    <p className="text-sm text-gray-600 mb-4">
                        パートナーとのセックスや
                        マスターベーションの
                        記録を登録することができます。
                    </p>
                    <div className="grid grid-cols-2 gap-4">
                        <button className="bg-gray-700 text-white p-4 rounded-lg">
                            記録する
                        </button>
                        <button className="bg-gray-700 text-white p-4 rounded-lg">
                            一覧を見る
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}