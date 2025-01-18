import React from 'react';
import { Link } from '@inertiajs/react';

export default function List({ logs }) {
    return (
        <div className="max-w-2xl mx-auto p-4">
            <div className="bg-white rounded-lg shadow p-6">
                <div className="mb-6">
                    <h2 className="text-xl font-bold">
                        {new Date().toLocaleDateString()} から過去30日間
                    </h2>
                </div>

                <div className="space-y-4">
                    {logs.map((log) => (
                        <div 
                            key={log.date} 
                            className="border-b pb-4"
                        >
                            <div className="font-semibold text-lg">
                                {new Date(log.date).toLocaleDateString()}
                            </div>
                            {log.records.length === 0 ? (
                                <div className="text-gray-500">記録なし</div>
                            ) : (
                                <div className="mt-2">
                                    {log.records.map((record) => (
                                        <div key={record.id} className="flex items-center space-x-4">
                                            <span className="text-gray-600">
                                                {record.type === 'sex' ? 'セックス' : 'マスターベーション'}:
                                            </span>
                                            <span>{record.count}回</span>
                                            {record.memo && (
                                                <span className="text-gray-500 text-sm">
                                                    {record.memo}
                                                </span>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    ))}
                </div>

                <div className="mt-6">
                    <Link
                        href={route('intimacy.index')}
                        className="inline-block bg-gray-200 px-4 py-2 rounded hover:bg-gray-300"
                    >
                        戻る
                    </Link>
                </div>
            </div>
        </div>
    );
}