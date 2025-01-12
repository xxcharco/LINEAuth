// resources/js/Pages/Conditions/Index.jsx
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ auth, message , conditions }) {
    const { data, setData, post, processing, reset } = useForm({
        is_high: null,
        condition: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('conditions.store'), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="min-h-screen bg-gray-100">
                <div className="max-w-2xl mx-auto pt-8 px-4">
                    {/* ヘッダー部分 - 戻るボタンを削除 */}
                    <div className="flex items-center mb-6">
                        <h1 className="text-center flex-1 font-bold">tawagram</h1>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        {/* 高まっているかの選択 */}
                        <div className="bg-white rounded-lg p-4 shadow-sm">
                            <h2 className="flex items-center text-gray-700 mb-4 font-medium">
                                今日は「高まっている」?「高まっていない」?
                            </h2>
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
                            <h2 className="flex items-center text-gray-700 mb-4 font-medium">
                                今日の体調は？
                            </h2>
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

                        {/* 送信ボタン */}
                        <button
                            type="submit"
                            disabled={processing || !data.is_high || !data.condition}
                            className="w-full bg-black text-white p-4 rounded-lg disabled:opacity-50"
                        >
                            記録する
                        </button>
                    </form>

                    {message && (
                        <div className="bg-green-50 p-4 rounded-lg mt-4">
                            <div className="flex items-center text-green-600">
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{message}</span>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}