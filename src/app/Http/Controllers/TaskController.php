<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Notifications\TaskCreated;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $pairId = $user->pair_id;
        $today = now();

        $tasks = Task::where('pair_id', $pairId)
            ->where(function ($query) use ($today) {
                $query->whereDate('due_date', '>=', $today)
                    ->orWhere(function ($q) use ($today) {
                        $q->whereDate('due_date', '<', $today)
                            ->where('is_done', false);
                    });
            })
            ->orderBy('due_date')
            ->get()
            ->map(function ($task) {
                $task->is_due_soon = !$task->is_done && \Carbon\Carbon::parse($task->due_date)->isBetween(now(), now()->addDays(3));
                return $task;
            })
            // 月ごとにグループ化（例: '2025年04月' => [tasks...])
            ->groupBy(function ($task) {
                return \Carbon\Carbon::parse($task->due_date)->format('Y年m月');
            });

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
        ]);

        $user = Auth::user();
        $pairId = $user->pair_id;
    
        if (!$pairId) {
            return redirect()->back()->with('error', 'ペアを設定してください。');
        }    

        $task = Task::create([
            'pair_id' => $pairId,
            'title' => $request->title,
            'due_date' => $request->due_date,
            'is_done' => false,
        ]);

        $partner = \App\Models\User::where('pair_id', $pairId)
                ->where('id', '!=', $user->id)
                ->first();

        if ($partner) {
            $task->user = $user; // 通知クラス用に user をセット
            $partner->notify(new TaskCreated($task));
        }

        return redirect()->route('tasks.index')->with('success', '作業を登録しました！');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorizeAccess($task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeAccess($task);

        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'is_done' => 'boolean',
        ]);

        $task->update([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'is_done' => $request->is_done ?? false,
        ]);

        return redirect()->route('tasks.index')->with('success', '作業を更新しました！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeAccess($task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', '作業を削除しました！');
    }

    // ペアチェック用（他のペアのタスクを編集できないようにする）
    private function authorizeAccess(Task $task)
    {
        if ($task->pair_id !== Auth::user()->pair_id) {
            abort(403, 'この作業にアクセスできません');
        }
    }

    public function toggle(Task $task)
    {
        if ($task->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        $task->update([
            'is_done' => !$task->is_done,
        ]);

        return redirect()->route('tasks.index')->with('success', '状態を更新しました');
    }
}
