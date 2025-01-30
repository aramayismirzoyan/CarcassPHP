<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Validators\NewUserValidator;
use App\Validators\UpdateUserValidator;
use Providers\PDOProvider;

class UserController extends Controller
{
    public function __construct(private readonly Request $request)
    {
    }

    public function get()
    {
        $users = UserRepository::getFiltered();

        if (empty($users)) {
            return Response::sendNotFoundError();
        }

        return Response::send([
            'success' => true,
            'result' => [
                'users' => $users
            ]
        ]);
    }

    public function show($userId)
    {
        $connection = PDOProvider::create();
        $repository = new UserRepository($connection);

        if ($user = $repository->hasUser($userId)) {
            return Response::send([
                'success' => true,
                'result' => [
                    'users' => $user
                ]
            ]);
        }

        return Response::sendNotFoundError();
    }

    public function create(UserService $service)
    {
        $data = $this->request->getJsonData();

        $validator = NewUserValidator::create($data);

        if (!NewUserValidator::create($data)->validate()) {
            return Response::sendValidatorError([
                'success' => false,
                'result' => [
                    'errors' => $validator->getErrors()
                ]
            ]);
        }

        return $service->create($data);
    }

    public function update(UserService $service, int $userId)
    {
        $data = $this->request->getJsonData();

        $validator =  UpdateUserValidator::create($data);

        if (!$validator->validate()) {
            return Response::sendValidatorError([
                'success' => false,
                'result' => [
                    'errors' => $validator->getErrors()
                ]
            ]);
        }

        return $service->update($userId, $data);
    }

    public function delete(UserRepository $repository)
    {
        if ($repository->delete()) {
            return Response::send([
                'success' => true,
            ]);
        }

        return Response::sendServerError();
    }

    public function deleteById(UserRepository $repository, $userId): string
    {
        if ($user = $repository->hasUser($userId)) {
            if ($repository->deleteById($userId)) {
                return Response::send([
                    'success' => true,
                    'result' => $user[0]
                ]);
            }

            return Response::sendServerError();
        } else {
            return Response::sendNotFoundError();
        }
    }
}
