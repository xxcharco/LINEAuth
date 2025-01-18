import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Create() {
    const { data, setData, post, processing } = useForm({
        start_date: new Date().toISOString().split('T')[0]  // 今日の日付をデフォルト値に
    });

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <Link href="/menstruation" className="text-gray-400">
                        ←
                    </Link>
                    <h1 className="text-center flex-1 font-bold">生理がきた</h1>
                </div>

                <form className="space-y-4">
                    {/* 日付入力 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <h2 className="text-lg font-medium mb-4">生理開始日</h2>
                        <input
                            type="date"
                            value={data.start_date}
                            onChange={e => setData('start_date', e.target.value)}
                            className="w-full p-2 border rounded"
                        />
                    </div>

                    {/* 登録ボタン */}
                    <button
                        onClick={(e) => {
                            e.preventDefault();
                            post('/menstruation');
                        }}
                        disabled={processing}
                        className="w-full bg-black text-white p-4 rounded-lg disabled:opacity-50"
                    >
                        登録する
                    </button>
                </form>
            </div>
        </div>
    );
}