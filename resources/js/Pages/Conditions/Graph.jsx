import { Link } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Graph({ auth, conditions }) {
    const [selectedCondition, setSelectedCondition] = useState(null);

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="min-h-screen bg-gray-100">
                <div className="max-w-2xl mx-auto pt-8 px-4">
                    {/* ヘッダー */}
                    <div className="flex items-center mb-6">
                        <Link href={route('conditions.index')} 
                        className="text-gray-400">
                            ←
                        </Link>
                        <h1 className="text-center flex-1 font-bold">記録を振り返る</h1>
                    </div>

                    {/* 日々の記録一覧 */}
                    <div className="space-y-4">
                    {conditions.map((condition) => {
                        // 日付チェック用のコード（前述の通り）
                        const today = new Date();
                        const yesterday = new Date(today);
                        yesterday.setDate(today.getDate() - 1);
                        const conditionDate = new Date(condition.date);

                        return (
                            <div key={condition.id} className="bg-white rounded-lg p-4 shadow-sm">
                                {/* 日付部分 */}
                                <div className="flex items-center justify-between mb-2">
                                    <div className="text-gray-600">{condition.date}</div>
                                    {condition.can_edit && (
                                        <Link 
                                            href={`/conditions/date/${condition.date}`}
                                            className="text-blue-500 text-sm"
                                        >
                                            編集
                                        </Link>
                                    )}
                                </div>
                                
                                {/* セックスしたい度の表示を修正 */}
                                <div className="mb-2">
                                    <span className="text-sm text-gray-600 mr-2">セックスしたい度：</span>
                                    <span className="text-sm font-medium">
                                        {condition.desire_level === 4 ? 'したい' :
                                        condition.desire_level === 3 ? 'ややしたい' :
                                        condition.desire_level === 2 ? 'ややしたくない' :
                                        'したくない'}
                                    </span>
                                </div>

                                {/* 体調の表示を修正 */}
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
                        );
                    })}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}