<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function showTaskCollection()
    {
        $tasks = Task::all();
        $types = array_unique($tasks->pluck('type')->toArray());
        $result = [];

        foreach ($types as $type) {
            foreach ($tasks as $task) {
                if ($task->type == $type) {
                    $result[$type][] = $task->toArray();
                }
            }
        }

        return view('tasks.collection', [
            'result' => $result,
        ]);
    }

    public function showTask($id)
    {
        $task = Task::query()->find($id);

        return view('tasks.task', [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
        ]);
    }
}
