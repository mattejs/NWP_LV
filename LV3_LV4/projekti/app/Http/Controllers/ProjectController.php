<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectUsers;
use App\Models\User;

class ProjectController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    function userProjects() {
        $projects = Auth::user()->projects;
        return view('projects', [
            'projects' => $projects
        ]);
    }

    function newProjectView() {
        return view('newproject');
    }

    function newProject (Request $request) {
        $project = new Project;
        $project->name = $request->name;
        $project->description = $request->description;
        $project->price = $request->price;
        $project->jobs_done = $request->jobsdone;
        $project->start_date = $request->startdate;
        $project->end_date = $request->enddate;
        $project->leader = Auth::user()->getId();
        $project->save();
    
        $project_user = new ProjectUsers();
        $project_user->user_id = Auth::user()->getId();
        $project_user->project_id = $project->id;
        $project_user->save();
        return redirect('/projects');
    }

    function editProjectView ($project_id = null) {
        $project = Project::find($project_id);
        return view('editproject', [
            'project' => $project
        ]);
    }

    function editProject (Request $request, $project_id = null) {
        $project = Project::find($project_id);
        if ($project->leader == Auth::user()->getId()) {
            $project->name = $request->name;
            $project->description = $request->description;
            $project->price = $request->price;
            $project->start_date = $request->startdate;
            $project->end_date = $request->enddate;
        }
        $project->jobs_done = $request->jobsdone;
        $project->save();
        return redirect('/projects');
    }

    function addUserOnProjectView($project_id = null) {
        $users = User::orderBy('created_at', 'asc')->get();
        return view('users', [
            'users' => $users,
            'project_id' => $project_id
        ]);
    }

    function addUserOnProject(Request $request) {
        $project_user = new ProjectUsers();
        $project_user->user_id = $request->user_id;
        $project_user->project_id = $request->project_id;
        $project_user->save();
        return redirect('/projects');
    }
}
