<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntimacyLogRequest;
use App\Models\IntimacyLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class IntimacyLogController extends Controller
{
    public function index()
    {
        return Inertia::render('IntimacyLog/Index');
    }

    public function store(StoreIntimacyLogRequest $request)
    {
        $log = new IntimacyLog($request->validated());
        $log->user_id = auth()->id();
        $log->save();

        return redirect()->route('intimacy.complete');
    }

    public function list()
    {
        $logs = IntimacyLog::where('user_id', auth()->id())
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date')
            ->map(function ($records) {
                return [
                    'date' => $records->first()->date,
                    'records' => $records
                ];
            })
            ->values();

        return Inertia::render('IntimacyLog/List', [
            'logs' => $logs
        ]);
    }

    public function storeBatch(Request $request)
{
    $user_id = auth()->id();

    Log::info('Batch Request Data:', $request->all());

    if ($request->has('sex')) {
        $sexData = $request->input('sex');
        $sexLog = IntimacyLog::create([
            'user_id' => $user_id,
            'type' => IntimacyLog::TYPE_SEX,  // 定数を使用
            'date' => $sexData['date'],
            'count' => $sexData['count'] !== '' ? (int)$sexData['count'] : null,  // 空文字列の場合はnull
            'memo' => $sexData['memo'] ?: null,
        ]);
        Log::info('Created Sex Log:', $sexLog->toArray());
    }

    if ($request->has('masturbation')) {
        $masturbationData = $request->input('masturbation');
        $masturbationLog = IntimacyLog::create([
            'user_id' => $user_id,
            'type' => IntimacyLog::TYPE_MASTURBATION,  // 定数を使用
            'date' => $masturbationData['date'],
            'count' => $masturbationData['count'] !== '' ? (int)$masturbationData['count'] : null,  // 空文字列の場合はnull
            'memo' => $masturbationData['memo'] ?: null,
        ]);
        Log::info('Created Masturbation Log:', $masturbationLog->toArray());
    }

    return redirect()->route('intimacy.complete');
}

    public function complete()
    {
        return Inertia::render('IntimacyLog/Complete');
    }
}