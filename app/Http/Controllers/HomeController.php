<?php

namespace App\Http\Controllers;

use App\Http\Models\HomeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{

    public $model = null;
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function deleteTask(Request $request){
        if(!Auth::user()->hasRole('manager')) return;

        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result =  $this->model->deleteTask();
        return response($result);
    }

    public function editTask(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result = $this->model->editTask();
        return response($result);
    }

    public function createTask(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result = $this->model->createTask();
        return response($result);
    }

    public function createComment(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result = $this->model->createcomment();
        return response($result);
    }

    public function editComment(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result = $this->model->editComment();
        return response($result);
    }

    public function deleteComment(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $this->model = new HomeModel($params);
        $result = $this->model->deleteComment();
        return response($result);
    }

    public function getComments(Request $request){
        $params = $request->post('params');
        $params = array_merge(['emp_id' => Auth::user()->emp_id], $params);

        $ownComments = $params['own_comments'] ?? false;

        $this->model = new HomeModel($params);
        $comments = $this->model->getComments($ownComments);
        return response($comments);
    }

    public function getTasks(Request $request){
        $params = $request->post('params');
        $ownTasks = $params['own_tasks'];
        $this->model = new HomeModel(['emp_id' => Auth::user()->emp_id]);
        $tasks = $this->model->getTasks($ownTasks);

        return response($tasks);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->model = new HomeModel(['emp_id' => Auth::user()->emp_id]);
        $data = [
            'tasks' => $this->model->getTasks(),
            'comments' => $this->model->getComments( !Auth::user()->hasRole('manager')),
            'employees' => $this->model->getEmployees(),
            'projects' => $this->model->getProjects()
        ];
        return view('home', $data);
    }
}
