<?php

namespace Test\Application;

use App\Container\ContainerFactory;
use App\Controllers\UserController;
use App\Helpers\Request;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Exception;
use Providers\PDOProvider;
use Test\Helpers\IntegrationTestCase;

class UserControllerTest extends IntegrationTestCase
{
    public function getRequestMock($data)
    {
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getJsonData'])
            ->getMock();

        $mock->method('getJsonData')
            ->willReturn($data);

        return $mock;
    }

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

        $requestMock = $this->getRequestMock([]);

        $result = (new UserController($requestMock))->get();

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

        $requestMock = $this->getRequestMock([]);

        $result = (new UserController($requestMock))->show($user['id']);

        $this->assertEquals(json_encode($data), $result);
    }

    public function test_create()
    {
        $requestMock = $this->getRequestMock([
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ]);

        $container = ContainerFactory::create();

        try {
            $service = $container->get(UserService::class);
            $result = (new UserController($requestMock))->create($service);

            $this->assertEquals('{"success":true,"result":{"id":1}}', $result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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

        $requestMock = $this->getRequestMock($newData);

        $container = ContainerFactory::create();

        try {
            $service = $container->get(UserService::class);
            $result = (new UserController($requestMock))->update($service, $user['id']);
            $result = json_decode($result, true);

            $connection = PDOProvider::create();

            $repository = new UserRepository($connection);

            $user = $repository->hasUser($user['id']);

            $this->assertEquals($newData['full_name'], $user[0]['full_name']);
            $this->assertEquals($newData['full_name'], $result['result']['full_name']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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

        $requestMock = $this->getRequestMock([]);

        $container = ContainerFactory::create();

        try {
            $repository = $container->get(UserRepository::class);
            $result = (new UserController($requestMock))->delete($repository);

            $connection = PDOProvider::create();
            $count = $connection->execute('SELECT COUNT(id) as count FROM users')[0]['count'];

            $this->assertEquals(0, $count);
            $this->assertEquals('{"success":true}', $result);
        } catch (Exception $e) {
            echo  $e->getMessage();
        }

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

        $requestMock = $this->getRequestMock([]);

        $container = ContainerFactory::create();

        try {
            $repository = $container->get(UserRepository::class);

            $result = (new UserController($requestMock))->deleteById($repository, $user['id']);

            $connection = PDOProvider::create();

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
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
