import { useForm, Link } from '@inertiajs/react';
import { useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ auth, message, currentDate }) {

    const today = new Date().toISOString().split('T')[0];
    const displayDate = currentDate || today;

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
        console.log('Submitting data:', {
            desire_level: data.desire_level,
            condition: data.condition
        }); // デバッグ用
    
        post(route('conditions.store'), {
            desire_level: data.desire_level,
            condition: data.condition
        }, {
            onSuccess: () => {
                console.log('Success!'); // デバッグ用
                reset();
            },
            onError: (errors) => {
                console.error('Errors:', errors);
            }
        });
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

    const desireLevels = [1, 2, 3, 4, 5];

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="min-h-screen bg-gray-100">
                <div className="max-w-2xl mx-auto pt-8 px-4">
                    {/* ヘッダー */}
                    <div className="flex items-center justify-between w-full">
                        {/* 左矢印 */}
                        <Link
                            href={`/conditions/date/${getPreviousDate(displayDate)}`}
                            className="text-gray-400 w-8 text-center"
                        >
                            ＜
                        </Link>

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

                    <form onSubmit={submit} className="space-y-8">
                        {/* セックスしたい度の選択 */}
                        <div className="bg-white rounded-lg p-6 shadow-sm">
                            <h2 className="text-center text-lg mb-6">今日のセックスしたい度は？</h2>
                            <div className="relative">
                                <div className="flex justify-between items-center px-4 mb-2">
                                    {desireLevels.map((level) => (
                                        <button
                                            key={level}
                                            type="button"
                                            onClick={() => setData('desire_level', level)}
                                            className={`w-14 h-14 rounded-full flex items-center justify-center text-xl
                                                ${data.desire_level === level 
                                                ? 'bg-yellow-400 text-white' 
                                                : 'bg-yellow-100'}`}
                                        >
                                            {level}
                                        </button>
                                    ))}
                                </div>
                                <div className="flex justify-between text-sm text-gray-500 px-4">
                                    <span>したくない</span>
                                    <span>したい</span>
                                </div>
                                <div className="absolute inset-x-4 top-1/2 -translate-y-1/2 -z-10">
                                    <div className="h-0.5 bg-gray-200"></div>
                                </div>
                            </div>
                        </div>

                        {/* 体調の選択 */}
                        <div className="bg-white rounded-lg p-6 shadow-sm">
                            <h2 className="text-center text-lg mb-6">今日の体調は？</h2>
                            <div className="grid grid-cols-3 gap-4">
                                {[
                                    { label: '良い', emoji: '😄' },
                                    { label: '普通', emoji: '😐' },
                                    { label: '悪い', emoji: '😫' }
                                ].map(({ label, emoji }) => (
                                    <button
                                        key={label}
                                        type="button"
                                        onClick={() => setData('condition', label)}
                                        className={`aspect-square rounded-lg flex flex-col items-center justify-center
                                            ${data.condition === label 
                                            ? 'bg-yellow-400 text-white' 
                                            : 'bg-yellow-100'}`}
                                    >
                                        <span className="text-2xl mb-2">{emoji}</span>
                                        <span>{label}</span>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* 記録ボタン */}
                        <div className="fixed bottom-20 inset-x-0">
                            <div className="max-w-2xl mx-auto px-4">
                                <button
                                    type="submit"
                                    disabled={processing || !data.desire_level || !data.condition}
                                    className="w-full h-16 bg-yellow-400 text-white rounded-full disabled:opacity-50
                                        flex items-center justify-center text-lg font-medium"
                                >
                                    記録する
                                </button>
                            </div>
                        </div>
                    </form>
                    {/* ヘッダー */}
                    <div className="fixed bottom-0 inset-x-0 bg-white border-t">
                        <div className="max-w-2xl mx-auto px-4">
                            <div className="flex justify-between py-2">
                                {/* ホーム */}
                                <Link href={route('conditions.index')} 
                                    className="flex flex-col items-center text-xs text-blue-500">
                                    <svg className="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>HOME</span>
                                </Link>

                                {/* グラフ */}
                                <Link href={route('conditions.graph')} 
                                    className="flex flex-col items-center text-xs text-gray-400">
                                    <svg className="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <span>グラフ</span>
                                </Link>

                                {/* 月経管理 */}
                                <Link href={route('menstruation.index')} 
                                    className="flex flex-col items-center text-xs text-gray-400">
                                    <svg className="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>月経管理</span>
                                </Link>

                                {/* セックス管理 */}
                                <Link href="#" className="flex flex-col items-center text-xs text-gray-400">
                                    <svg className="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>セックス管理</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}