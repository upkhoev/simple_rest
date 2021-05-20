<?php

namespace App\Collection;


use App\Api;

class Categories extends Api
{
    public $apiName = 'categories';
    protected $tableName = 'categories';

    public function viewAction()
    {
        if ($id = $this->getId()) {
            $cat = $this->getById($id);
            if ($cat) {
                if ($this->requestUri[0] === 'products') {
                    $cat['products'] = $this->db->getAll('SELECT * FROM products p INNER JOIN product_categories pc on p.id = pc.product_id WHERE pc.category_id = ?;', [$id]);
                }
                return $this->response($cat, 200);
            }
        }
        return $this->errorResponse('Data not found', 404);
    }

    protected function createAction()
    {
        $name = $this->requestParams['name'] ?? '';
        $description = $this->requestParams['description'] ?? '';
        if ($name) {
            $item = $this->db->add("INSERT INTO {$this->tableName}(name,description) VALUES (:name, :description);", [
                'name' => $name,
                'description' => $description
            ]);

            //var_dump($this->db->sth->errorInfo());
            if ($item) {
                return $this->successResponse('Data saved.');
            }
        }

        return $this->errorResponse("Saving error", 500);
    }

    protected function updateAction()
    {
        $id = $this->getId();
        if (!$id || !$this->getById($id)) {
            return $this->errorResponse("Category with id=$id not found", 404);
        }

        $name = $this->requestParams['name'] ?? '';
        $description = $this->requestParams['description'] ?? '';

        if ($name) {
            $res = $this->db->set("UPDATE {$this->tableName} 
                SET name = :name, description = :description WHERE
                id = :id;", [
                    'name' => $name,
                    'description' => $description,
                    'id' => $id
            ]);
            if ($res){
                return $this->successResponse('Data updated.');
            }
        }
        return $this->errorResponse("Update error", 400);
    }
}
