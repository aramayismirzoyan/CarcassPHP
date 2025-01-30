<?php

namespace Test\Unit;

use App\Helpers\Response;
use App\Repositories\UserRepository;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function createRepositoryMock($methods)
    {
        return $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }

    public function test_create_method()
    {
        $repository = $this->createRepositoryMock(['create']);

        $repository->method('create')
            ->willReturn(1);

        $service = new UserService($repository);

        $expects = [
            'success' => true,
            'result' => [
                'id' => 1
            ],
        ];

        $result = $service->create([]);

        $this->assertEquals(json_encode($expects), $result);
    }

    public function test_create_method_when_data_have_not_added()
    {
        $repository = $this->createRepositoryMock(['create']);

        $repository->method('create')
            ->willReturn(false);

        $service = new UserService($repository);

        $expects = Response::sendServerError();

        $result = $service->create([]);

        $this->assertEquals($expects, $result);
    }

    public function test_update_method()
    {
        $repository = $this->createRepositoryMock(['update', 'hasUser']);

        $returnedData = [
            'id' => 1,
            'full_name' => 'Nick',
            'role' => 'designer',
            'efficiency' => 2
        ];

        $newData = [
            'id' => 1,
            'full_name' => 'John',
            'role' => 'developer',
            'efficiency' => 10
        ];

        $repository->method('hasUser')
            ->willReturn($returnedData);

        $repository->method('update')
            ->willReturn([$newData]);

        $service = new UserService($repository);

        $result = $service->update(1, $newData);

        $expected = Response::send([
            'success' => true,
            'result' => $newData
        ]);

        $this->assertEquals($expected, $result);
    }

    public function test_update_method_id_has_not_user()
    {
        $repository = $this->createRepositoryMock(['update', 'hasUser']);

        $repository->method('hasUser')
            ->willReturn(false);

        $repository->method('update')
            ->willReturn([]);

        $service = new UserService($repository);

        $result = $service->update(1, []);

        $expected = Response::sendNotFoundError();

        $this->assertEquals($expected, $result);
    }

    public function test_update_method_when_not_valid_fields_is_given()
    {
        $repository = $this->createRepositoryMock(['update', 'hasUser']);

        $user = [
            'id' => 1,
            'full_name' => 'Nick',
            'role' => 'designer',
            'efficiency' => 2
        ];

        $repository->method('hasUser')
            ->willReturn([$user]);

        $repository->method('update')
            ->willReturn([]);

        $service = new UserService($repository);

        $result = $service->update(1, [
            'age' => 1
        ]);

        $expected = Response::send([
            'success' => true,
            'result' => $user
        ]);

        $this->assertEquals($expected, $result);
    }

    public function test_update_method_when_update_failed()
    {
        $repository = $this->createRepositoryMock(['update', 'hasUser']);

        $data = [
            'full_name' => 'Nick',
            'role' => 'designer',
            'efficiency' => 2
        ];

        $repository->method('hasUser')
            ->willReturn(['id' => 1, ...$data]);

        $repository->method('update')
            ->willReturn(false);

        $service = new UserService($repository);

        $result = $service->update(1, $data);

        $expected = Response::sendServerError();

        $result = $service->update(1, $data);

        $this->assertEquals($expected, $result);
    }
}
