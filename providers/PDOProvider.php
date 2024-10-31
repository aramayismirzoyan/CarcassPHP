<?php

namespace Providers;

use PDO;

class PDOProvider
{
    private static PDOProvider $object;
    private PDO $connection;

    private final function  __construct()
    {
        $config = include('./config/db.php');

        $this->connection = $this->getConnection($config);
    }

    private function getConnection($config): PDO
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=UTF8";
        return new PDO($dsn, $config['user'], $config['password']);
    }

    public static function create(): PDOProvider
    {
        if(!isset(self::$object)) {
            self::$object = new PDOProvider();
        }

        return self::$object;
    }

    public function get(string $sql): array
    {
        $query = $this->connection->query($sql);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWithParams(string $sql, $params): array
    {
        $query = $this->connection->prepare($sql);

        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($sql, $params): int|false
    {
        $query= $this->connection->prepare($sql);
        if($query->execute($params)) {
            return $this->connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function update(string $sql, array $params): array|false
    {
        $query = $this->connection->prepare($sql);

        foreach ($params as $key => &$value) {

            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;

            $query->bindParam(":{$key}", $value, $paramType);
        }

        if($query->execute()) {
            $sql = "SELECT * FROM users WHERE id=:id";

            return $this->getWithParams($sql, [
                'id' => $params['id']
            ]);
        } else {
            return false;
        }
    }

    public function delete() :bool
    {
        $query = $this->connection->prepare("DELETE from users");
        return $query->execute();
    }

    public function deleteById($userId) :bool
    {
        $sql = "DELETE FROM users WHERE id=?";
        $query = $this->connection->prepare($sql);
        return $query->execute([$userId]);
    }
}