<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Notifications\TaskCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $today = now();

        $tasks = Task::where('pair_id', $this->pair->id)
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
                $task->is_due_soon = !$task->is_done && Carbon::parse($task->due_date)->isBetween(now(), now()->addDays(3));
                return $task;
            })
            ->groupBy(function ($task) {
                return Carbon::parse($task->due_date)->format('Y年m月');
            });

        return view('tasks.index', compact('tasks'));
    }

    public function store(TaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $task = Task::create([
                'pair_id' => $this->pair->id,
                'title' => $request->title,
                'due_date' => $request->due_date,
                'is_done' => false,
            ]);

            $partner = User::where('pair_id', $this->pair->id)
                ->where('id', '!=', $this->authUser->id)
                ->first();

            if ($partner) {
                $task->user = $this->authUser;
                $partner->notify(new TaskCreated($task));
            }

            DB::commit();
            return redirect()->route('tasks.index')->with('success', '作業を登録しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '作業の登録中にエラーが発生しました。')->withInput();
        }
    }

    public function edit(Task $task)
    {
        $this->authorizeAccess($task);
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorizeAccess($task);

        $task->update([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'is_done' => $request->is_done ?? false,
        ]);

        return redirect()->route('tasks.index')->with('success', '作業を更新しました！');
    }

    public function destroy(Task $task)
    {
        $this->authorizeAccess($task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', '作業を削除しました！');
    }

    private function authorizeAccess(Task $task)
    {
        if ($task->pair_id !== $this->pair->id) {
            abort(403, 'この作業にアクセスできません');
        }
    }

    public function toggle(Task $task)
    {
        $this->authorizeAccess($task);

        $task->update([
            'is_done' => !$task->is_done,
        ]);

        return redirect()->route('tasks.index')->with('success', '状態を更新しました');
    }
}
