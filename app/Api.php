<?php

namespace App;

use http\Exception\RuntimeException;

class Api
{
    public $requestParams;
    public $requestUri = [];

    public $apiName = '';
    protected $method = '';
    protected $tableName;
    protected $action = '';

    /**
     * @var Db
     */
    protected $db;

    public function __construct()
    {
        $this->requestUri = explode('/', trim($_REQUEST['action'],'/'));
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST') {
            $this->requestParams = $_POST;
        }
        if ($this->method == 'PUT') {
            // x-www-form-urlencoded
            $putData = file_get_contents("php://input");
            parse_str($putData, $this->requestParams);
            $this->method = 'PUT';
        }
    }

    public function setDb(Db $db)
    {
        $this->db = $db;
    }

    private function requestStatus($code)
    {
        $status = [
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    protected function response($data, int $status = 500)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    protected function successResponse(string $message)
    {
        return $this->response(['message' => $message], 200);
    }

    protected function errorResponse($message, int $status)
    {
        return $this->response(['message' => $message], $status);
    }

    public function run()
    {
        if (array_shift($this->requestUri) !== $this->apiName) {
            throw new RuntimeException('API Not Found', 404);
        }
        //Определение действия для обработки
        $this->action = $this->getAction();

        //Если метод(действие) определен в дочернем классе API
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function getAction()
    {
        $method = $this->method;

        switch ($method) {
            case 'GET':
                if ($this->requestUri) {
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    /**
     * Метод GET
     * Вывод списка всех записей
     * @return string
     */
    public function indexAction()
    {
        $items = $this->getAll();
        if ($items){
            return $this->response($items, 200);
        }
        return $this->errorResponse('Data not found', 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * @return string
     */
    public function viewAction()
    {
        $id = $this->getId();

        if ($id) {
            $item = $this->getById($id);
            if ($item) {
                return $this->response($item, 200);
            }
        }
        return $this->errorResponse('Data not found', 404);
    }

    /**
     * Метод DELETE
     * @return string
     */
    protected function deleteAction()
    {
        $id = $this->getId();
        if (!$id || !($this->getById($id))) {
            return $this->errorResponse("{$this->apiName} with id=$id not found", 404);
        }

        if ($this->deleteById($id)) {
            return $this->successResponse('Data deleted.');
        }
        return $this->errorResponse('Delete error', 500);
    }

    /**
     * get collection id for methods - (get,put,delete)
     * @return mixed
     */
    protected function getId()
    {
        return array_shift($this->requestUri);
    }

    protected function getAll()
    {
        return $this->db->getAll("SELECT * FROM {$this->tableName}");
    }

    protected function getById($id)
    {
        return $this->db->getRow("SELECT * FROM {$this->tableName} WHERE id = ?", [$id]);
    }

    protected function deleteById($id)
    {
        return $this->db->set("DELETE FROM {$this->tableName} WHERE id = ?", [$id]);
    }
}
