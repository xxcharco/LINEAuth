import { Link } from '@inertiajs/react';
import { useState } from 'react';

export default function Graph({ conditions }) {
    const [selectedCondition, setSelectedCondition] = useState(null);

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <Link href="/conditions" className="text-gray-400">
                        ←
                    </Link>
                    <h1 className="text-center flex-1 font-bold">記録を振り返る</h1>
                </div>

                {/* 日々の記録一覧 */}
                <div className="space-y-4">
                    {conditions.map((condition) => (
                        <div key={condition.id} className="bg-white rounded-lg p-4 shadow-sm">
                            {/* 日付 */}
                            <div className="flex items-center justify-between mb-2">
                                <div className="text-gray-600">{condition.date}</div>
                                <Link 
                                    href={route('conditions.edit', condition.id)}
                                    className="text-blue-500 text-sm"
                                >
                                    編集
                                </Link>
                            </div>
                            
                            {/* 高まり状態 */}
                            <div className="mb-2">
                                <span className={`inline-block px-3 py-1 rounded-full text-sm ${
                                    condition.is_high 
                                    ? 'bg-blue-100 text-blue-800' 
                                    : 'bg-gray-100 text-gray-800'
                                }`}>
                                    {condition.is_high ? '高まっている' : '高まっていない'}
                                </span>
                            </div>

                            {/* 体調 */}
                            <div className="flex items-center">
                                <span className="text-sm text-gray-600 mr-2">体調：</span>
                                <span className={`text-sm font-medium ${
                                    condition.condition === '良い' ? 'text-green-600' :
                                    condition.condition === 'やや良い' ? 'text-blue-600' :
                                    condition.condition === 'やや悪い' ? 'text-yellow-600' :
                                    'text-red-600'
                                }`}>
                                    {condition.condition}
                                </span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}