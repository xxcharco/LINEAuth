import React from 'react';
import { useForm, Link, router } from '@inertiajs/react';
import Footer from '@/Components/Footer';

export default function Index() {
    // セックス記録用のフォーム
    const sexForm = useForm({
        date: new Date().toISOString().split('T')[0],
        type: 'sex',
        count: '',
        memo: ''
    });

    // マスターベーション記録用のフォーム
    const masturbationForm = useForm({
        date: new Date().toISOString().split('T')[0],
        type: 'masturbation',
        count: '',
        memo: ''
    });

    // 今日の日付
    const today = new Date().toISOString().split('T')[0];

    // セックス記録の送信
    const handleSexSubmit = (e) => {
        e.preventDefault();
        sexForm.post(route('intimacy.store'));
    };

    // マスターベーション記録の送信
    const handleMasturbationSubmit = (e) => {
        e.preventDefault();
        masturbationForm.post(route('intimacy.store'));
    };

    // 一括登録
    const handleBatchSubmit = (e) => {
        e.preventDefault();
        router.post(route('intimacy.storeBatch'), {
            sex: sexForm.data,
            masturbation: masturbationForm.data
        });
    };

    return (
        <div className="max-w-2xl mx-auto p-4">
            <div className="bg-white rounded-lg shadow-lg p-6">
                <h1 className="text-2xl font-bold mb-6">なかよしログ</h1>
                
                {/* セックス記録セクション */}
                <form onSubmit={handleSexSubmit} className="mb-8">
                    <div className="mb-6">
                        <h2 className="text-lg font-semibold mb-4">セックス管理</h2>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">した日</label>
                                <input
                                    type="date"
                                    value={sexForm.data.date}
                                    onChange={(e) => sexForm.setData('date', e.target.value)}
                                    max={today}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                {sexForm.errors.date && <div className="text-red-500 text-sm mt-1">{sexForm.errors.date}</div>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">回数（任意）</label>
                                <input
                                    type="number"
                                    min={0}
                                    value={sexForm.data.count}
                                    onChange={(e) => sexForm.setData('count', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">メモ（任意）</label>
                                <textarea
                                    value={sexForm.data.memo}
                                    onChange={(e) => sexForm.setData('memo', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    rows={3}
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={sexForm.processing}
                        className="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
                    >
                        セックスを記録する
                    </button>
                </form>

                {/* マスターベーション記録セクション */}
                <form onSubmit={handleMasturbationSubmit} className="mb-8">
                    <div className="mb-6">
                        <h2 className="text-lg font-semibold mb-4">マスターベーション管理</h2>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">した日</label>
                                <input
                                    type="date"
                                    value={masturbationForm.data.date}
                                    onChange={(e) => masturbationForm.setData('date', e.target.value)}
                                    max={today}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                {masturbationForm.errors.date && <div className="text-red-500 text-sm mt-1">{masturbationForm.errors.date}</div>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">回数（任意）</label>
                                <input
                                    type="number"
                                    min={0}
                                    value={masturbationForm.data.count}
                                    onChange={(e) => masturbationForm.setData('count', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">メモ（任意）</label>
                                <textarea
                                    value={masturbationForm.data.memo}
                                    onChange={(e) => masturbationForm.setData('memo', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    rows={3}
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={masturbationForm.processing}
                        className="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:opacity-50"
                    >
                        マスターベーションを記録する
                    </button>
                </form>

                {/* 一括登録ボタン */}
                <button
                    onClick={handleBatchSubmit}
                    disabled={sexForm.processing || masturbationForm.processing}
                    className="w-full bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 disabled:opacity-50 mb-4"
                >
                    両方を記録する
                </button>

                {/* ログ一覧へのリンク */}
                <Link
                    href={route('intimacy.list')}
                    className="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200"
                >
                    記録を確認する
                </Link>
            </div>
            <Footer />
        </div>
    );
}