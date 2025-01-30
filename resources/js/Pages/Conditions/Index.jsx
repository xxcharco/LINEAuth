import { useForm, Link } from '@inertiajs/react';
import { useEffect } from 'react';
import Footer from '@/Components/Footer';

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
                                {desireLevels.map(({ level, label }) => (
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
                            <div className="grid grid-cols-2 gap-4">
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
                </div>
                <Footer />
            </div>
    );
}