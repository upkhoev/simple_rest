<?php

namespace App;

use PDO;
use PDOException;

class Db
{
    public static $dsn = 'pgsql:dbname=test;host=localhost';
    public static $user = 'postgres';
    public static $pass = 'password';

    /**
     * Объект PDO.
     * @var PDO
     */
    public $dbh = null;

    /**
     * Statement Handle.
     * @var \PDOStatement
     */
    public $sth = null;

    /**
     * Выполняемый SQL запрос.
     */
    public $query = '';

    /**
     * Подключение к БД
     */
    public function __construct()
    {
        if (!$this->dbh) {
            try {
                $this->dbh = new PDO(
                    self::$dsn,
                    self::$user,
                    self::$pass
                );
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            } catch (PDOException $e) {
                exit('Error connecting to database: ' . $e->getMessage());
            }
        }
    }

    /**
     * Добавление в таблицу, в случаи успеха вернет вставленный ID, иначе 0.
     * @param $query
     * @param array $params
     * @return int|string
     */
    public function add($query, $params = [])
    {
        $this->sth = $this->dbh->prepare($query);
        return $this->sth->execute((array) $params) ? $this->dbh->lastInsertId() : 0;
    }

    /**
     * Выполнение запроса.
     * @param $query
     * @param array $param
     * @return bool
     */
    public function set($query, $param = [])
    {
        $this->sth = $this->dbh->prepare($query);
        return $this->sth->execute((array) $param);
    }

    /**
     * Получение строки из таблицы.
     * @param $query
     * @param array $param
     * @return mixed
     */
    public function getRow($query, $param = [])
    {
        $this->sth = $this->dbh->prepare($query);
        $this->sth->execute((array) $param);
        return $this->sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Получение всех строк из таблицы.
     * @param $query
     * @param array $param
     * @return array
     */
    public function getAll($query, $param = []):array
    {
        $this->sth = $this->dbh->prepare($query);
        $this->sth->execute((array) $param);
        return $this->sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получение значения.
     * @param $query
     * @param array $param
     * @param null $default
     * @return mixed|null
     */
    public function getValue($query, $param = [], $default = null)
    {
        $result = $this->getRow($query, $param);
        if (!empty($result)) {
            $result = array_shift($result);
        }

        return (empty($result)) ? $default : $result;
    }

    /**
     * Получение столбца таблицы.
     * @param string $query
     * @param array $param
     * @return array
     */
    public function getColumn($query, $param = [])
    {
        $this->sth = $this->dbh->prepare($query);
        $this->sth->execute((array) $param);
        return $this->sth->fetchAll(PDO::FETCH_COLUMN);
    }
}
