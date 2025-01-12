// resources/js/Pages/Conditions/Edit.jsx
import { Link, useForm } from '@inertiajs/react';

export default function Edit({ condition }) {
    const { data, setData, put, processing } = useForm({
        is_high: condition.is_high,
        condition: condition.condition,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('conditions.update', condition.id), {
            onSuccess: () => {
                // グラフページにリダイレクト
            },
        });
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <Link href={route('conditions.graph')} className="text-gray-400">
                        ←
                    </Link>
                    <h1 className="text-center flex-1 font-bold">記録を編集</h1>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    {/* 記録日 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <div className="text-gray-600 mb-2">
                            記録日: {condition.recorded_date}
                        </div>
                    </div>

                    {/* 高まっているかの選択 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <h3 className="flex items-center text-gray-700 mb-4 font-medium">
                            「高まっている」?「高まっていない」?
                        </h3>
                        <div className="space-y-2">
                            <button
                                type="button"
                                onClick={() => setData('is_high', true)}
                                className={`w-full p-4 rounded-lg border ${
                                    data.is_high === true 
                                    ? 'bg-blue-500 text-white border-blue-500' 
                                    : 'bg-white border-gray-300'
                                }`}
                            >
                                高まっている
                            </button>
                            <button
                                type="button"
                                onClick={() => setData('is_high', false)}
                                className={`w-full p-4 rounded-lg border ${
                                    data.is_high === false 
                                    ? 'bg-blue-500 text-white border-blue-500' 
                                    : 'bg-white border-gray-300'
                                }`}
                            >
                                高まっていない
                            </button>
                        </div>
                    </div>

                    {/* 体調の選択 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <h3 className="flex items-center text-gray-700 mb-4 font-medium">
                            体調は？
                        </h3>
                        <div className="grid grid-cols-2 gap-2">
                            {['良い', 'やや良い', 'やや悪い', '悪い'].map((option) => (
                                <button
                                    key={option}
                                    type="button"
                                    onClick={() => setData('condition', option)}
                                    className={`p-4 rounded-lg border ${
                                        data.condition === option 
                                        ? 'bg-blue-500 text-white border-blue-500' 
                                        : 'bg-white border-gray-300'
                                    }`}
                                >
                                    {option}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* 更新ボタン */}
                    <div className="space-y-2">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-black text-white p-4 rounded-lg disabled:opacity-50"
                        >
                            更新する
                        </button>
                        <Link
                            href={route('conditions.graph')}
                            className="block w-full text-center p-4 rounded-lg border border-gray-300"
                        >
                            キャンセル
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}