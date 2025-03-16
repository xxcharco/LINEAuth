import React from 'react';
import { useForm, Link, router } from '@inertiajs/react';
import { useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ auth, message, currentDate }) {

    const today = new Date().toISOString().split('T')[0];
    const displayDate = currentDate || today;

    // 前日の日付を計算（ここに移動）
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split('T')[0];

    const { data, setData, post, processing, reset } = useForm({
        desire_level: null,
        condition: null,
    });

    // ページロード時に本日の記録を取得
    useEffect(() => {
        const fetchTodayData = async () => {
            try {
                const response = await window.axios.get(`/api/conditions/${displayDate}`);
                if (response.data) {
                    setData({
                        desire_level: response.data.desire_level,
                        condition: response.data.condition
                    });
                }
            } catch (error) {
                console.error('Error fetching today\'s data:', error);
            }
        };

        fetchTodayData();
    }, [displayDate]);

    const submit = (e) => {
        e.preventDefault();
        
        const submitData = {
            desire_level: data.desire_level,
            condition: data.condition,
            recorded_date: displayDate
        };
    
        // 送信データをコンソールに出力
        console.log('About to submit:', submitData);
    
        // 送信オプションを明示的に指定
        const options = {
            preserveScroll: true,
            onBefore: () => console.log('Before submit'),
            onSuccess: () => console.log('Success'),
            onError: (errors) => console.error('Errors:', errors),
            onFinish: () => console.log('Finish')
        };
    
        // 送信実行
        post(route('conditions.store'), submitData, options);
    };

    // 日付のフォーマット関数
    const formatDate = (dateStr) => {
        const date = new Date(dateStr || new Date());
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        return `${year}年${month}月${day}日`;
    };

    // 前日の日付を取得
    const getPreviousDate = (dateStr) => {
        const date = new Date(dateStr || new Date());
        date.setDate(date.getDate() - 1);
        return date.toISOString().split('T')[0];
    };

    const getNextDate = (dateStr) => {
        const date = new Date(dateStr || new Date());
        date.setDate(date.getDate() + 1);
        return date.toISOString().split('T')[0];
    };

    const desireLevels = [
        { level: 1, label: 'したくない' },
        { level: 2, label: 'ややしたくない' },
        { level: 3, label: 'ややしたい' },
        { level: 4, label: 'したい' }
    ];

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-2xl mx-auto pt-8 px-4 pb-16">
                <div className="bg-white rounded-lg shadow-lg p-6">
                    <div className="flex items-center justify-between w-full">
                        {/* 左矢印 - 表示中の日付が今日か昨日の場合のみ表示 */}
                        {displayDate > yesterdayStr && (
                            <Link
                                href={`/conditions/date/${getPreviousDate(displayDate)}`}
                                className="text-gray-400 w-8 text-center"
                            >
                                ＜
                            </Link>
                        )}
                        {/* 昨日より前の日付の場合は空のスペース */}
                        {displayDate <= yesterdayStr && (
                            <div className="w-8"></div>
                        )}

                        {/* 日付（中央配置） */}
                        <span className="text-gray-600 flex-grow text-center">
                            {formatDate(displayDate)}
                        </span>

                        {/* 右矢印（今日以降は空のスペース） */}
                        {displayDate < today ? (
                            <Link
                                href={`/conditions/date/${getNextDate(displayDate)}`}
                                className="text-gray-400 w-8 text-center"
                            >
                                ＞
                            </Link>
                        ) : (
                            <div className="w-8"></div>  
                        )}
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        {/* セックスしたい度の選択 */}
                        <div className="bg-white rounded-lg p-4 shadow-sm">
                            <h3 className="flex items-center text-gray-700 mb-4 font-medium">
                                今日のセックスしたい度は？
                            </h3>
                            <div className="space-y-2">
                                {desireLevels.map(({ level, label }) => (
                                    <button
                                        key={level}
                                        type="button"
                                        onClick={() => setData('desire_level', level)}
                                        className={`w-full p-4 rounded-lg border ${
                                            data.desire_level === level 
                                            ? 'bg-blue-500 text-white border-blue-500' 
                                            : 'bg-white border-gray-300'
                                        }`}
                                    >
                                        {label}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* 体調の選択 */}
                        <div className="bg-white rounded-lg p-4 shadow-sm">
                            <h3 className="flex items-center text-gray-700 mb-4 font-medium">
                                今日の体調は？
                            </h3>
                            <div className="grid grid-cols-2 gap-2">
                                {[
                                    { label: '良い', emoji: '😄' },
                                    { label: 'やや良い', emoji: '😊' },
                                    { label: 'やや悪い', emoji: '😕' },
                                    { label: '悪い', emoji: '😫' }
                                ].map(({ label, emoji }) => (
                                    <button
                                        key={label}
                                        type="button"
                                        onClick={() => setData('condition', label)}
                                        className={`p-4 rounded-lg border ${
                                            data.condition === label 
                                            ? 'bg-blue-500 text-white border-blue-500' 
                                            : 'bg-white border-gray-300'
                                        }`}
                                    >
                                        <span className="text-lg mb-1">{emoji}</span>
                                        <span>{label}</span>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* 記録ボタン */}
                        <div className="space-y-2">
                            <button
                                type="submit"
                                disabled={processing || !data.desire_level || !data.condition}
                                className="w-full bg-black text-white p-4 rounded-lg disabled:opacity-50"
                            >
                                記録する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}