<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $tasks = Task::all();
        
        return view('tasks.index', compact('tasks'));
    }
    
    /**
     * @param  Request  $request
     * @param $task_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function read(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        
        return response(compact('task'));
    }
    
    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $title = $request->title;
        if ($title == '') {
            if (!$request->ajax()) {
                return redirect()->back()->with(['error' => 'Title cannot be empty.']);
            } else {
                abort(400);
            }
        }
        $task = Task::create(['title' => $title, 'completed' => false])->save();
        
        return redirect()->route('tasks.index')->with(['message' => 'Task created.']);
    }
    
    /**
     * @param  Request  $request
     * @param  int  $task_id
     * @param  boolean|int  $completed
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, int $task_id, $completed = null)
    {
        $task = Task::findOrFail($task_id);
        
        if ($completed !== null) {
            $task->completed = (int) $completed;
        } else {
            $title = $request->title;
            // there are better ways to handle validation in laravel, but for one field this is quick and dirty
            if ($title == '') {
                if (!$request->ajax()) {
                    return redirect()->back()->with(['error' => 'Title cannot be empty.']);
                } else {
                    abort(400, 'Title cannot be empty.');
                }
            }
            $task->title = $title;
        }
        
        $task->save();
        
        if (!$request->ajax()) {
            return redirect()->route('tasks.index')->with(['message' => 'Task Updated.']);
        } else {
            // normally, we would probably handle the message on the frontend and handle reloading the data in the current page
            // but since we are just refreshing on successful update of the form, but not for toggling completed status
            // flash a message to the session
            if ($completed === null) {
                session()->flash('message', 'Task Updated.');
            }
            return response(['status' => 'success']);
        }
    }
    
    /**
     * @param  Request  $request
     * @param  int  $task_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function delete(Request $request, int $task_id)
    {
        $task = Task::findOrFail($task_id);
        
        $task->delete();
        
        if (!$request->ajax()) {
            return redirect()->route('tasks.index')->with(['message' => 'Task has beed deleted.']);
        } else {
            return response(['status' => 'success']);
        }
    }
}
