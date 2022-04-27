<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Task;
use App\Models\UserTasks;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->role == 'nastavnik') {
            $allTasks = Task::all()->where('profesor', '=', Auth::user()->id);
            $tasks = [];
    
            foreach($allTasks as $task) {
                $userTasks = UserTasks::all()->where('task_id', '=', $task->id);
                foreach($userTasks as $userTask) {
                    $tasks[] = [
                        User::all()->where('id', '=', $userTask->student_id)->first(),
                        $task
                    ];
                }
            }
        }
        else {
            $allTasks = Task::all()->where('assignee', '=', null);
            $tasks = [];

            foreach($allTasks as $task) {
                $userTasks = UserTasks::all()->where('task_id', '=', $task->id)->where('student_id', '=', Auth::user()->id)->first();
                if($userTasks == null) {
                    $tasks[] = [
                        Auth::user(),
                        $task
                    ];
                }
            }
        }        
        return view('home', compact('tasks'));
    }

    public function admin() 
    {
        $users = User::all();
        return view('admin', compact('users'));
    }

    public function changeRole(Request $request) 
    {
        $request = $request->all();
        $user = User::where('id', '=', $request['id'])->first();
        $user->role = $request['role'];
        $user->save();
        
        $users = User::all();
        return view('admin', compact('users'));
    }
}