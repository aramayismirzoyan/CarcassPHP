<?php

namespace App\Repositories;

use App\Helpers\Request;
use Providers\PDOProvider;

class UserRepository
{
    public function __construct(private readonly PDOProvider $connection)
    {
    }

    public function create($data): int|false
    {
        $sql = 'INSERT INTO users (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)';

        return $this->connection->insert($sql, [
            'full_name' => $data['full_name'],
            'role' => $data['role'],
            'efficiency' => $data['efficiency'],
        ]);
    }

    public static function getFiltered(): array
    {
        $connection = PDOProvider::create();

        $sql = 'SELECT * FROM users';

        $role = (new Request())->getParam($_GET, 'role');

        if ($role !== '') {
            $sql .= ' WHERE role=:role';
            $users = $connection->getWithParams($sql, [
                'role' => $role
            ]);
        } else {
            $users = $connection->execute($sql);
        }

        return $users;
    }

    public static function getById($id): array
    {
        $connection = PDOProvider::create();

        $sql = "SELECT * FROM users WHERE id=:id";

        return $connection->getWithParams($sql, [
            'id' => $id
        ]);
    }

    public function hasUser($id): array|false
    {
        $user = self::getById($id);

        return !empty($user) ? $user : false;
    }

    private function addUpdatedFieldsInSQL(array $data, array $fields): array
    {
        $sql = [];
        $params = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $params[$field] = $data[$field];
                $sql[] = "{$field}=:{$field}";
            }
        }

        return [
            'sql' => implode(', ', $sql),
            'params' => $params,
        ];
    }

    public function update($data, $userId): array|false
    {
        $sql = "UPDATE users SET ";

        $fields = $this->addUpdatedFieldsInSQL($data, ['full_name', 'role', 'efficiency']);

        $fields['params']['id'] = $userId;

        $sql .= $fields['sql'];

        $sql .= " WHERE id=:id";

        return $this->connection->update($sql, $fields['params'], 'users');
    }

    public function delete()
    {
        return $this->connection->delete('users');
    }

    public function deleteById($userId)
    {
        return $this->connection->deleteById($userId, 'users');
    }
}
