<?php

namespace App\Repositories;

use App\Helpers\Request;
use Providers\PDOProvider;

class UserRepository
{
    public static function create($data): int|false
    {
        $connection = PDOProvider::create();

        $sql = 'INSERT INTO users (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)';

        return $connection->insert($sql, [
            'full_name' => $data['full_name'],
            'role' => $data['role'],
            'efficiency' => $data['efficiency'],
        ]);
    }

    public static function getFiltered() :array
    {
        $connection = PDOProvider::create();

        $sql = 'SELECT * FROM users';

        $role = Request::getParam($_GET, 'role');

        if($role !== '') {
            $sql .= ' WHERE role=:role';
            $users = $connection->getWithParams($sql, [
                'role' => $role
            ]);
        } else {
            $users = $connection->get($sql);
        }

        return $users;
    }

    public static function getById($id) :array
    {
        $connection = PDOProvider::create();

        $sql = "SELECT * FROM users WHERE id=:id";

        return $connection->getWithParams($sql, [
            'id' => $id
        ]);
    }

    public static function hasUser($id): array|false
    {
        $user = self::getById($id);

        return !empty($user) ? $user : false;
    }

    private function addUpdatedFieldsInSQL(array $data, array $fields): array
    {
        $sql = [];
        $params = [];

        foreach ($fields as $field) {
            if(isset($data[$field])) {
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
        $connection = PDOProvider::create();

        $sql = "UPDATE users SET ";

        $fields = $this->addUpdatedFieldsInSQL($data, ['full_name', 'role', 'efficiency']);

        $fields['params']['id'] = $userId;

        $sql .= $fields['sql'];

        $sql .= " WHERE id=:id";

        return $connection->update($sql, $fields['params']);
    }
}