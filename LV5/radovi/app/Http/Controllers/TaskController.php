<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Task;
use App\Models\UserTasks;

class TaskController extends Controller
{
    public function index() {
        return view('new');
    }

    public function store(Request $request) {
        $request->validate([
            'title_hr' => 'required|string|unique:tasks',
            'title_en' => 'required|string|unique:tasks',
            'task' => 'required|string',
            'study_type' => 'required|string'
        ]);

        $task = new Task();
        $task->title_hr = $request->title_hr;
        $task->title_en = $request->title_en;
        $task->task = $request->task;
        $task->profesor = Auth::user()->id;
        $task->assignee = null;
        $task->study_type = $request->study_type;
        $task->save();

        return redirect()->route('home');
    }

    public function acceptAssignee($studentId, $taskId) {
        $task = Task::all()->where('id', '=', $taskId)->first();
        $userTasks = UserTasks::where('task_id', '=', $taskId)->delete();

        $task->assignee = $studentId;
        $task->save();
        return redirect()->route('home');
    }

    public function taskSign($studentId, $taskId) {

        $userTasks = new UserTasks();
        $userTasks->student_id = $studentId;
        $userTasks->task_id = $taskId;
        $userTasks->save();
        
        return redirect()->route('home');
    }
}