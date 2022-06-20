<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\SubdivisionModel;
use App\Models\TaskModel;
use App\Models\CommentModel;
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
        $this->model = new SubdivisionModel();
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
        $this->model = new SubdivisionModel(['subdiv_id' => $id]);
        $subdiv = $this->model->getSubdivision();

        return $this->prepareResponse([
            'subdiv' => $subdiv,
        ]);
    }

    /**
     * Обновление подразделения
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
            'subdiv_id' => 'nullable|integer',
            'subdiv_title' => 'required|string',
            'subdiv_desc' => 'required|string',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new SubdivisionModel($params);
        $result = $this->model->updateSubdiv();

        $successText = isset($params['subdiv_id']) ? 'Подразделение успешно обновлено' : 'Подразделение успешно создано';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление подразделения
     * @param Request $request
     * @return mixed
     */
    public function deleteSubdiv(Request $request)
    {
        $this->params = [
            'subdiv_id',
        ];
        $this->validatorRules = [
            'subdiv_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $subdiv = SubdivisionModel::find($params['subdiv_id']);
        $attrs = $subdiv->toArray();
        if(count($attrs['subdiv_emps']) > 0) {
            return [
                'status' => false,
                'type' => 'error',
                'message' => 'В подразделении остались сотрудники'
            ];
        } else {
            $result = $subdiv->delete();
        }

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Подразделение успешно удалено' : 'Произошла ошибка'
        ];
    }
}
