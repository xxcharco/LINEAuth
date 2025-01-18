import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import Footer from '@/Components/Footer';

export default function Edit({ menstruation }) {
    const { data, setData, put, delete: destroy, processing } = useForm({
        start_date: menstruation.start_date,
        end_date: menstruation.end_date || ''
    });

    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('menstruation.update', menstruation.id));
    };

    const handleDelete = () => {
        destroy(route('menstruation.destroy', menstruation.id));
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="max-w-2xl mx-auto pt-8 px-4">
                {/* ヘッダー */}
                <div className="flex items-center mb-6">
                    <Link href="/menstruation" className="text-gray-400">
                        ←
                    </Link>
                    <h1 className="text-center flex-1 font-bold">生理日を編集</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* 開始日 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <h2 className="text-lg font-medium mb-4">生理開始日</h2>
                        <input
                            type="date"
                            value={data.start_date}
                            onChange={e => setData('start_date', e.target.value)}
                            className="w-full p-2 border rounded"
                        />
                    </div>

                    {/* 終了日 */}
                    <div className="bg-white rounded-lg p-4 shadow-sm">
                        <h2 className="text-lg font-medium mb-4">生理終了日</h2>
                        <input
                            type="date"
                            value={data.end_date}
                            min={data.start_date} // 開始日より前の日付を選択できないように
                            onChange={e => setData('end_date', e.target.value)}
                            className="w-full p-2 border rounded"
                        />
                    </div>

                    {/* ボタングループ */}
                    <div className="space-y-3">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-black text-white p-4 rounded-lg disabled:opacity-50"
                        >
                            保存する
                        </button>
                        
                        <button
                            type="button"
                            onClick={() => setShowDeleteConfirm(true)}
                            className="w-full bg-white text-red-600 border border-red-600 p-4 rounded-lg"
                        >
                            削除する
                        </button>
                    </div>
                </form>

                {/* 削除確認モーダル */}
                {showDeleteConfirm && (
                    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                        <div className="bg-white p-6 rounded-lg max-w-sm w-full mx-4">
                            <h3 className="text-lg font-medium mb-4">
                                本当に記録を削除しますか？
                            </h3>
                            <div className="flex space-x-4">
                                <button
                                    onClick={() => setShowDeleteConfirm(false)}
                                    className="flex-1 p-3 border rounded-lg"
                                >
                                    キャンセル
                                </button>
                                <button
                                    onClick={handleDelete}
                                    className="flex-1 p-3 bg-red-600 text-white rounded-lg"
                                >
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
            <Footer />
        </div>
    );
}