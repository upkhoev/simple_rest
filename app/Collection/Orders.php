<?php

namespace App\Collection;


use App\Api;
use http\Exception\RuntimeException;

class Orders extends Api
{
    public $apiName = 'orders';
    protected $tableName = 'orders';

    protected static $defaultDeliveryPrice = 500; // default price
    protected static $firstOrderDeliveryPrice = 100; // price first order by user
    protected static $discountRetiredUser = 0.05; // in percent

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
                $userRow = $this->db->getRow('SELECT is_retired FROM phpauth_users WHERE id = ?', [$item['user_id']]);
                $userOrdersCntRow = $this->db->getRow("SELECT count(*) AS cnt FROM orders WHERE user_id = ? AND id <> ?", [$item['user_id'], $item['id']]);

                $item['delivery_price'] = self::$defaultDeliveryPrice;
                if ($item['total'] > 10000) {
                    // free ship
                    $item['delivery_price'] = 0;
                } elseif ($userOrdersCntRow['cnt'] == 1) {
                    // first order by user
                    $item['delivery_price'] = self::$firstOrderDeliveryPrice;
                } elseif (self::$discountRetiredUser && $item['delivery_price'] > 0 && $userRow && $userRow['is_retired']) {
                    $item['delivery_price'] = round($item['delivery_price'] - $item['delivery_price'] * self::$discountRetiredUser);
                }
                return $this->response($item, 200);
            }
        }
        return $this->errorResponse('Data not found', 404);
    }

    public function indexAction()
    {
        throw new RuntimeException('Not found Method', 404);
    }

    public function deleteAction()
    {
        throw new RuntimeException('Not found Method', 404);
    }
}
