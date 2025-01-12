<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(auth()->id());
        $conditions = Condition::orderBy('recorded_date', 'desc')->get();
        
        return Inertia::render('Conditions/Index', [
            'conditions' => $conditions,
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
        $request->validate([
            'is_high' => 'required|boolean',
            'condition' => 'required|in:良い,やや良い,やや悪い,悪い',
        ]);
    
        $condition = new Condition($request->input());
        $condition->recorded_date = now();
        $condition->save();
    
        return Inertia::render('Conditions/Complete');
    }

    /**
     * グラフ表示とデータ一覧
     */
    public function graph()
    {
        $conditions = Condition::orderBy('recorded_date', 'desc')
            ->get()
            ->map(function ($condition) {
                return [
                    'id' => $condition->id,
                    'date' => \Carbon\Carbon::parse($condition->recorded_date)->format('Y-m-d'),
                    'is_high' => $condition->is_high,
                    'condition' => $condition->condition,
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
}
