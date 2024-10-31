<?php

namespace App;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Providers\PDOProvider;

class UserController
{
    public function create()
    {
        $data = Request::getJson();

        $validator = (new UserService())->getNewUserValidator($data);

        if(!$validator->validate()) {
            Response::sendValidatorError([
                'success' => false,
                'result' => [
                    'errors' => $validator->getErrors()
                ]
            ]);
        }

        $id = UserRepository::create($data);

        if(is_int($id)) {
            Response::send([
                'success' => true,
                'result' => [
                    'id' => $id
                ],
            ]);
        } else {
            Response::sendServerError();
        }
    }

    public function get()
    {
        $users = UserRepository::getFiltered();

        if(empty($users)) {
            Response::sendNotFoundError();
        }

        Response::send([
            'success' => true,
            'result' => [
                'users' => $users
            ]
        ]);
    }

    public function show($userId)
    {
        if($user = UserRepository::hasUser($userId)) {
            Response::send([
                'success' => true,
                'result' => [
                    'users' => $user
                ]
            ]);
        }

        Response::sendNotFoundError();
    }

    public function update($userId)
    {
        $data = Request::getJson();

        $userService = new UserService();
        $validator = $userService->getUpdateUserValidator($data);

        if(!$validator->validate()) {
            Response::sendValidatorError([
                'success' => false,
                'result' => [
                    'errors' => $validator->getErrors()
                ]
            ]);
        }

        $user = UserRepository::hasUser($userId);

        if(!$user) {
            Response::sendNotFoundError();
        }

        if(!Validator::hasAtLeastInData($data, [
            'full_name', 'role', 'efficiency'
        ])) {
            Response::send([
                'success' => true,
                'result' => $user[0]
            ]);
        }

        if($result = (new UserRepository)->update($data, $userId)) {
            Response::send([
                'success' => true,
                'result' => $result[0]
            ]);
        }

        Response::sendServerError();
    }

    public function delete()
    {
        $connection = PDOProvider::create();

        if($connection->delete()) {
            Response::send([
                'success' => true,
            ]);
        }

        Response::sendServerError();
    }

    public function deleteById($userId)
    {
        $connection = PDOProvider::create();

        if($user = UserRepository::hasUser($userId)) {
            if($connection->deleteById($userId)) {
                Response::send([
                    'success' => true,
                    'result' => $user[0]
                ]);
            }

            Response::sendServerError();
        } else {
            Response::sendNotFoundError();
        }
    }
}