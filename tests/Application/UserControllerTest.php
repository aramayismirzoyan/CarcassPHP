<?php

namespace Test\Application;

use App\Container\ContainerFactory;
use App\Repositories\UserRepository;
use Exception;
use Providers\PDOProvider;
use Test\Helpers\IntegrationTestCase;

class UserControllerTest extends IntegrationTestCase
{
    public function createUser($user)
    {
        $connection = PDOProvider::create();
        $repository = new UserRepository($connection);
        $id = $repository->create($user);

        return ['id' => $id, ...$user];
    }

    public function test_get()
    {
        $user = $this->createUser([
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ]);

        $result = $this->get('/get');

        $data = [
                'success' => true,
                'result' => [
                    'users' => [
                        $user
                    ]
                ]
        ];

        $this->assertEquals(json_encode($data), $result);
    }

    public function test_show(): void
    {
        $user = $this->createUser([
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ]);

        $data = [
            'success' => true,
            'result' => [
                'users' => [
                    $user
                ]
            ]
        ];

        $result = $this->get("/get/{$user['id']}");

        $this->assertEquals(json_encode($data), $result);
    }

    public function test_create()
    {
        $result = $this->post('/create', [
                'full_name' => 'John',
                'role' => 'developer',
                'efficiency' => 10
            ]);

        $this->assertEquals('{"success":true,"result":{"id":1}}', $result);
    }

    public function test_update()
    {
        $user = $this->createUser([
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ]);

        $newData = [
            'full_name' => 'Nick'
        ];

        $result = $this->patch("/update/{$user['id']}", $newData);

        $result = json_decode($result, true);

        try {
            $repository = ContainerFactory::create()->get(UserRepository::class);
        } catch (Exception $e) {
            return;
        }

        $updatedUser = $repository->hasUser($user['id']);

        $this->assertEquals($newData['full_name'], $updatedUser[0]['full_name']);
        $this->assertEquals($newData['full_name'], $result['result']['full_name']);
    }

    public function test_delete()
    {
        $data = [
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ];

        $this->createUser($data);
        $this->createUser($data);

        $result = $this->delete('/delete');

        try {
            $connection = ContainerFactory::create()->get(PDOProvider::class);
        } catch (Exception $e) {
            return;
        }

        $count = $connection->execute('SELECT COUNT(id) as count FROM users')[0]['count'];

        $this->assertEquals(0, $count);
        $this->assertEquals('{"success":true}', $result);
    }

    public function test_delete_by_id()
    {
        $data = [
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ];

        $user = $this->createUser($data);
        $secondUser = $this->createUser($data);

        $result = $this->delete("/delete/{$user['id']}");

        try {
            $connection = ContainerFactory::create()->get(PDOProvider::class);
        } catch (Exception $e) {
            return;
        }

        $userCount = $connection->getWithParams('SELECT COUNT(id) as count FROM users WHERE id=:id', [
            'id' => $user['id']
        ])[0]['count'];

        $this->assertEquals(0, $userCount);

        $secondUserCount = $connection->getWithParams('SELECT COUNT(id) as count FROM users WHERE id=:id', [
            'id' => $secondUser['id']
        ])[0]['count'];

        $this->assertEquals(1, $secondUserCount);

        $expected = [
            'success' => true,
            'result' => $user
        ];

        $this->assertEquals(json_encode($expected), $result);
    }
}
