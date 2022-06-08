<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\SubdivisionsModel;
use App\Models\TaskModel;
use App\Models\CommentsModel;
use App\Models\StatusModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubdivisionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'subdivisions';
    }

    /**
     * Страница подразделений
     * @param Request $request
     * @return mixed
     */
    public function getSubdivisionsPage(Request $request)
    {
        $this->model = new SubdivisionsModel();
        $subdivs = $this->model->getSubdivisions();

        return $this->prepareResponse([
            'subdivs' => $subdivs,
        ]);
    }

    /**
     * Страница подразделения
     * @param Request $request
     * @return mixed
     */
    public function getSubdivisionPage(Request $request, $id)
    {
        $this->params = [
            'subdiv_id'
        ];
        $this->validatorRules = [
            'subdiv_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['subdiv_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->defaultView = 'subdivision';
        $this->model = new SubdivisionsModel(['subdiv_id' => $id]);
        $subdiv = $this->model->getSubdivision();

        return $this->prepareResponse([
            'subdiv' => $subdiv,
        ]);
    }

    /**
     * Обновление статуса задачи
     * @param Request $request
     * @return mixed
     */
    public function updateSubdiv(Request $request)
    {
        $this->params = [
            'subdiv_id',
            'subdiv_title',
            'subdiv_desc',
        ];
        $this->validatorRules = [
            'subdiv_id' => 'required|integer',
            'subdiv_title' => 'required|string',
            'subdiv_desc' => 'required|string',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new SubdivisionsModel($params);
        $result = $this->model->updateSubdiv();

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Подразделение успешно обновлено' : 'Произошла ошибка'
        ];
    }
}
