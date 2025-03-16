<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\User;
use Carbon\Carbon; 
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($date = null)
    {
        $targetDate = $date ? Carbon::parse($date) : now();

        Log::info('Processing date:', ['date' => $targetDate->format('Y-m-d')]);

        // ユーザーIDでフィルタリングを追加
        $todayCondition = Condition::where('recorded_date', $targetDate->format('Y-m-d'))
            ->where('user_id', auth()->id())
            ->first();
        
        // デバッグログの追加
        Log::info('Today\'s condition:', ['condition' => $todayCondition]);

        return Inertia::render('Conditions/Index', [
            'currentDate' => $targetDate->format('Y-m-d'),
            'existingData' => $todayCondition,
            'message' => session('message')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Full request details:', [
            'all' => $request->all(),
            'referer' => $request->header('referer')
        ]);

        $validated = $request->validate([
            'desire_level' => 'required|integer|between:1,4',
            'condition' => 'required|in:良い,やや良い,やや悪い,悪い',
        ]);

        // リファラーからrecorded_dateを取得
        $recordedDate = null;
        if ($request->header('referer')) {
            if (preg_match('/\/date\/(\d{4}-\d{2}-\d{2})/', $request->header('referer'), $matches)) {
                $recordedDate = $matches[1];
            }
        }

        // validatedデータにuser_idを追加
        $data = array_merge($validated, [
            'user_id' => auth()->id()  // ここでuser_idを追加
        ]);

        $condition = Condition::updateOrCreate(
            [
                'recorded_date' => $recordedDate ?: now()->format('Y-m-d'),
                'user_id' => auth()->id()  // 検索条件にもuser_idを追加
            ],
            $data
        );

        Log::info('Saved condition:', $condition->toArray());

        return Inertia::render('Conditions/Complete');
    }

    /**
     * グラフ表示とデータ一覧
     */
    public function graph()
    {
        // ユーザーIDでフィルタリングを追加
        $conditions = Condition::where('user_id', auth()->id())
            ->orderBy('recorded_date', 'desc')
            ->get()
            ->map(function ($condition) {
                $recordDate = \Carbon\Carbon::parse($condition->recorded_date);
                $yesterday = now()->subDay()->startOfDay();

                return [
                    'id' => $condition->id,
                    'date' => $recordDate->format('Y-m-d'),
                    'desire_level' => $condition->desire_level,
                    'condition' => $condition->condition,
                    'can_edit' => $recordDate->greaterThanOrEqualTo($yesterday)
                ];
            });

        return Inertia::render('Conditions/Graph', [
            'conditions' => $conditions
        ]);
    }

    /**
     * 編集フォームの表示
     */
    public function edit(Condition $condition)
    {
        return Inertia::render('Conditions/Edit', [
            'condition' => [
                'id' => $condition->id,
                'is_high' => $condition->is_high,
                'recorded_date' => \Carbon\Carbon::parse($condition->recorded_date)->format('Y-m-d'),
                'condition' => $condition->condition,
            ]
        ]);
    }

    /**
     * データの更新処理
     */
    public function update(Request $request, Condition $condition)
    {
        $validated = $request->validate([
            'is_high' => 'required|boolean',
            'condition' => 'required|in:良い,やや良い,やや悪い,悪い',
        ]);

        $condition->update($validated);

        return redirect()->route('conditions.graph')
            ->with('message', '更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * データの削除処理
     */
    public function destroy(Condition $condition)
    {
        $condition->delete();
        
        return redirect()->route('conditions.graph')
            ->with('message', '削除しました');
    }

    public function getByDate($date)
    {
        $condition = Condition::where('recorded_date', $date)->first();
        return response()->json($condition);
    }
}
