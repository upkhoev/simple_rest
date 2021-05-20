<?php

namespace App\Collection;

use App\Api;

class Products extends Api
{
    public $apiName = 'products';
    protected $tableName = 'products';

    protected function createAction()
    {
        $name = $this->requestParams['name'] ?? '';
        $price = $this->requestParams['price'] ?? '';
        if ($name && $price) {
            $item = $this->db->add("INSERT INTO {$this->tableName}(name,price) VALUES (:name, :price);", [
                'name' => $name,
                'price' => $price
            ]);

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
            return $this->errorResponse("Product with id=$id not found", 404);
        }

        $name = $this->requestParams['name'] ?? '';
        $price = $this->requestParams['price'] ?? '';

        if ($name && $price) {
            $res = $this->db->set("UPDATE {$this->tableName} 
                SET name = :name, price = :price WHERE
                id = :id;", [
                'name'  => $name,
                'price' => $price,
                'id'    => $id
            ]);
            if ($res){
                return $this->successResponse('Product data updated.');
            }
        }
        return $this->errorResponse("Update error", 400);
    }
}
