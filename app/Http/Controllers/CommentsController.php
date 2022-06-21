<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\CommentModel;
use App\Models\SubdivisionModel;
use App\Models\CustomerModel;
use App\Models\User;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Обновление комментария
     * @param Request $request
     * @return mixed
     */
    public function saveComment(Request $request)
    {
        $this->params = [
            'comment_id',
            'comment_task',
            'comment_comment',
        ];
        $this->validatorRules = [
            'comment_id' => 'nullable|integer',
            'comment_task' => 'required|integer',
            'comment_comment' => 'required|string',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new CommentModel($params);
        $result = $this->model->updateComment();

        $successText = isset($params['comment_id']) ? 'Комментарий успешно обновлен' : 'Комментарий успешно создан';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление комментария
     * @param Request $request
     * @return mixed
     */
    public function deleteComment(Request $request)
    {
        $this->params = [
            'comment_id',
        ];
        $this->validatorRules = [
            'comment_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $comment = CommentModel::find($params['comment_id']);

        $result = $comment->delete();

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Комментарий успешно удален' : 'Произошла ошибка'
        ];
    }
}
