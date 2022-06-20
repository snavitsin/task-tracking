<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'customers';
    }

    /**
     * Страница заказчиков
     * @param Request $request
     * @return mixed
     */
    public function getCustomersPage(Request $request)
    {
        $this->model = new CustomerModel();
        $customers = $this->model->getCustomers();

        return $this->prepareResponse([
            'customers' => $customers,
        ]);
    }

    /**
     * Страница заказчика
     * @param Request $request
     * @return mixed
     */
    public function getCustomerPage(Request $request, $id)
    {
        $this->params = [
            'customer_id'
        ];
        $this->validatorRules = [
            'customer_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['customer_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->defaultView = 'customer';
        $this->model = new CustomerModel(['customer_id' => $id]);
        $customer = $this->model->getCustomer();

        return $this->prepareResponse([
            'customer' => $customer,
        ]);
    }

    /**
     * Обновление заказчика
     * @param Request $request
     * @return mixed
     */
    public function updateCustomer(Request $request)
    {
        $this->params = [
            'customer_id',
            'customer_fio',
            'customer_email',
            'customer_phone',
        ];
        $this->validatorRules = [
            'customer_id' => 'nullable|integer',
            'customer_fio' => 'required|string',
            'customer_email' => 'required|unique:customers,customer_email|email',
            'customer_phone' => ['required', 'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/'],
        ];

        $params = $this->getParam($request);
        $this->validatorRules['customer_email'] = isset($params['customer_id'])
            ? 'required|email'
            : 'required|unique:customers,customer_email|email';

        $this->validatorSpecialText =  [
            'customer_email.unique' => 'Email уже используется',
            'customer_phone.regex' => 'Неправильно введен номер телефона',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new CustomerModel($params);
        $result = $this->model->updateCustomer();

        $successText = isset($params['customer_id']) ? 'Заказчик успешно обновлен' : 'Заказчик успешно создан';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление заказчика
     * @param Request $request
     * @return mixed
     */
    public function deleteCustomer(Request $request)
    {
        $this->params = [
            'customer_id',
        ];
        $this->validatorRules = [
            'customer_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $customer = CustomerModel::find($params['customer_id']);
        $attrs = $customer->toArray();
        if(count($attrs['customer_projects']) > 0) {
            return [
                'status' => false,
                'type' => 'error',
                'message' => 'У заказчика остались проекты'
            ];
        } else {
            $result = $customer->delete();
        }

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Заказчик успешно удален' : 'Произошла ошибка'
        ];
    }
}
