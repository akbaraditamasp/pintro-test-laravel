<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            [
                'title' => $title,
                'group_id' => $group_id,
            ] = $request->validate([
                'title' => 'required|string',
                'group_id' => 'required|numeric'
            ]);

            $group = Group::findOrFail($group_id);

            $task = new Task();
            $task->title = $title;
            $task->group()->associate($group);

            $task->save();

            return $task->toArray();
        });
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        return DB::transaction(function () use ($request, $task) {
            [
                'title' => $title,
                'group_id' => $group_id,
            ] = $request->validate([
                'title' => 'required|string',
                'group_id' => 'required|numeric'
            ]);

            $group = Group::findOrFail($group_id);

            $task->title = $title;
            $task->group()->associate($group);

            $task->save();

            return $task->toArray();
        });
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return $task->toArray();
    }

    public function index(Request $request)
    {
        if ($request->user()->role === "employee") {
            return Task::query()
                ->with("group")
                ->whereHas(
                    "group",
                    fn($query) => $query->whereHas(
                        "users",
                        fn($query) => $query
                            ->where(
                                "users.id",
                                $request->user()->id
                            )
                    )
                )
                ->get()
                ->toArray();
        }
        return Task::query()->with("group")->get()->toArray();
    }
}
