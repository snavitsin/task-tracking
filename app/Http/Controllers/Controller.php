<?php

namespace App\Http\Controllers;

use App\Config\AppConfig;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultView = '';
    protected $params = [];
    protected $model = null;
    protected $validatorRules = [];
    protected $validatorSpecialText = [];
    protected $latestValidationErrors = [];

    /**
     * Returns Response
     * @param array $res
     * @param string $view
     * @param boolean $status
     * @param integer $httpStatus
     * @param array $headers
     * @access private
     * @return mixed
     */
    protected function prepareResponse($res = [], $status = true, $httpStatus = 200, $headers = [])
    {
        $data = AppConfig::get('isAjax')
            ? ['status' => $status, 'data' => $res]
            : view($this->defaultView, $res);

        return response($data, $httpStatus, $headers);
    }

    /**
     * Получение доступных параметров ввода
     * @param Request $request
     * @return array
     */
    protected function getParam(Request $request)
    {
        $param = $this->params;
        $filters = array_intersect(array_keys($request->all()), $param);
        return $request->only($filters);
    }

    /**
     * Проверка правильности входных данных
     * @param array $params
     * @return mixed
     */
    protected function isValidRequest($request, $params = [])
    {
        $params = array_merge($this->getParam($request), $params);
        $validator = $this->getValidator($params);
        $res = !$validator->fails();
        if (!$res)
            $this->latestValidationErrors = $validator->errors()->messages();
        return $res;
    }

    /**
     * Создание валидатора
     * @param array $params
     * @return mixed
     */
    protected function getValidator($params)
    {
        return Validator::make($params, $this->validatorRules, $this->validatorSpecialText);
    }

    /**
     * Returns error
     * @access protected
     * @param int $code
     * @return mixed
     */
    protected function getError($title, $code = 404)
    {
        if (AppConfig::get('isAjax')){
            return $this->prepareResponse([
                'status' => false,
                'code' => $code,
                'title' => $title,
            ], false, $code);
        }
        else{
            try {
                throw new \Exception('', $code);
            }
            catch (\Exception $e){

            }
        }
    }
}
