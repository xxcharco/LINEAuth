<?php

namespace App\Http\Controllers;

use App\Models\Menstruation;
use App\Http\Requests\StoreMenstruationRequest;
use App\Http\Requests\StoreMenstruationEndRequest;
use App\Http\Requests\UpdateMenstruationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class MenstruationController extends Controller
{
    public function index()
    {
        $records = Menstruation::orderBy('start_date', 'desc')
            ->take(3)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'start_date' => $record->start_date->format('Y年n月j日'),
                    'end_date' => $record->end_date ? $record->end_date->format('Y年n月j日') : null,
                ];
            });

        return Inertia::render('Menstruation/Index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $latestRecord = Menstruation::latest('start_date')->first();
        
        Log::info('Create action', [
            'request_type' => $request->query('type'),
            'latest_record' => $latestRecord,
            'has_end_date' => isset($latestRecord?->end_date)
        ]);

        // typeパラメータが'end'で、かつ最新の記録があり終了日がない場合
        if ($request->query('type') === 'end' && $latestRecord && !isset($latestRecord->end_date)) {
            return Inertia::render('Menstruation/CreateEnd');
        }

        // それ以外は開始日記録画面へ
        return Inertia::render('Menstruation/Create');
    }

    public function edit(Menstruation $menstruation)
    {
        return Inertia::render('Menstruation/Edit', [
            'menstruation' => [
                'id' => $menstruation->id,
                'start_date' => $menstruation->start_date->format('Y-m-d'),
                'end_date' => $menstruation->end_date?->format('Y-m-d'),
            ]
        ]);
    }

    public function update(UpdateMenstruationRequest $request, Menstruation $menstruation)
    {
        Log::info('Update menstruation request:', [
            'request_data' => $request->validated(),
            'menstruation_before' => $menstruation->toArray()
        ]);

        $menstruation->update($request->validated());

        Log::info('Menstruation after update:', [
            'menstruation_after' => $menstruation->fresh()->toArray()
        ]);

        // 完了画面へリダイレクト
        return Inertia::render('Menstruation/Complete');
    }

    public function destroy($id)
    {
        $menstruation = Menstruation::findOrFail($id);
        $menstruation->delete();

        return to_route('menstruation.index')
            ->with('message', '記録を削除しました');
    }

    public function store(StoreMenstruationRequest $request)
    {
        Menstruation::create($request->validated());

        return Inertia::render('Menstruation/Complete');
    }

    public function storeEnd(StoreMenstruationEndRequest $request)
    {
        $menstruation = Menstruation::latest()->first();
        $menstruation->update($request->validated());

        return Inertia::render('Menstruation/Complete');
    }
}