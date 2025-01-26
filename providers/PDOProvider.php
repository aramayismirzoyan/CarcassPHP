<?php

namespace Providers;

use App\Enums\ConfigPaths;
use PDO;

final class PDOProvider
{
    private static PDOProvider $object;
    private readonly PDO $connection;

    final private function __construct()
    {
        $config = include(ConfigPaths::DB->get());

        $this->connection = $this->getConnection($config);
    }

    public static function create(): PDOProvider
    {
        if (!isset(self::$object)) {
            self::$object = new PDOProvider();
        }

        return self::$object;
    }

    private function getDB($config)
    {
        if (defined('PHPUNIT_INTEGRATION_TESTSUITE')
            && PHPUNIT_INTEGRATION_TESTSUITE === true) {
            return $config['db_test'];
        }

        return $config['db'];
    }

    private function getConnection($config): PDO
    {
        $db = $this->getDB($config);

        $dsn = "mysql:host={$config['host']};dbname={$db};charset=UTF8";
        return new PDO($dsn, $config['user'], $config['password']);
    }

    public function execute(string $sql): array
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
        $query = $this->connection->prepare($sql);
        if ($query->execute($params)) {
            return $this->connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function update(string $sql, array $params, string $table): array|false
    {
        $query = $this->connection->prepare($sql);

        foreach ($params as $key => &$value) {

            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;

            $query->bindParam(":{$key}", $value, $paramType);
        }

        if ($query->execute()) {
            $sql = "SELECT * FROM `{$table}` WHERE id=:id";

            return $this->getWithParams($sql, [
                'id' => $params['id']
            ]);
        } else {
            return false;
        }
    }

    public function delete($table): bool
    {
        $query = $this->connection->prepare("DELETE from `{$table}`");
        return $query->execute();
    }

    public function deleteById($userId, $table): bool
    {
        $sql = "DELETE FROM `{$table}` WHERE id=?";
        $query = $this->connection->prepare($sql);
        return $query->execute([$userId]);
    }

    public function truncateTable($table): void
    {
        if (preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            $sql = "TRUNCATE TABLE `{$table}`";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        }
    }
}
