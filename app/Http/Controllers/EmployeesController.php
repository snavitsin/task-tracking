<?php

namespace App\Http\Controllers;

use App\Models\PositionModel;
use App\Models\PriorityModel;
use App\Models\StatusModel;
use App\Models\SubdivisionModel;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'employees';
    }

    /**
     * Страница сотрудников
     * @param Request $request
     * @return mixed
     */
    public function getEmpsPage(Request $request)
    {
        $this->model = new User();
        $emps = $this->model->getEmployees();

        return $this->prepareResponse([
            'emps' => $emps,
        ]);
    }

    /**
     * Страница сотрудника
     * @param Request $request
     * @return mixed
     */
    public function getEmpPage(Request $request, $id)
    {
        $this->params = [
            'emp_id'
        ];
        $this->validatorRules = [
            'emp_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['emp_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->defaultView = 'employee';
        $this->model = new User(['emp_id' => $id]);
        $emp = $this->model->getEmployee();

        $subdivisionsModel = new SubdivisionModel();
        $subdivisions = $subdivisionsModel->getSubdivisions();

        $positions = PositionModel::getPositions();

        $priorityModel = new PriorityModel();
        $statusModel = new StatusModel();
        $priority = $priorityModel->getPriority();
        $statuses = $statusModel->getStatuses();

        return $this->prepareResponse([
            'emp' => $emp,
            'subdivisions' => $subdivisions,
            'positions' => $positions,
            'priority' => $priority,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Обновление сотрудника
     * @param Request $request
     * @return mixed
     */
    public function updateEmployee(Request $request)
    {
        $this->params = [
            'emp_id',
            'emp_name',
            'emp_patroname',
            'emp_surname',
            'emp_subdiv',
            'emp_position',
            'emp_mail',
        ];
        $this->validatorRules = [
            'emp_id' => 'nullable|integer',
            'emp_name' => 'required|string',
            'emp_patroname' => 'required|string',
            'emp_surname' => 'required|string',
            'emp_subdiv' => 'required|integer',
            'emp_position' => 'required|integer',
        ];

        $params = $this->getParam($request);
        $this->validatorRules['emp_mail'] = isset($params['emp_id'])
            ? 'required|email'
            : 'required|unique:employees,emp_mail|email';

        $this->validatorSpecialText =  [
            'emp_mail.unique' => 'Email уже используется',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new User($params);
        $result = $this->model->updateEmp();

        $successText = isset($params['emp_id']) ? 'Сотрудник успешно обновлен' : 'Сотрудник успешно создан';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление сотрудника
     * @param Request $request
     * @return mixed
     */
    public function deleteEmployee(Request $request)
    {
        $this->params = [
            'emp_id',
        ];
        $this->validatorRules = [
            'emp_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $emp = User::find($params['emp_id']);
        $result = $emp->delete();

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Сотрудник успешно удален' : 'Произошла ошибка'
        ];
    }
}
