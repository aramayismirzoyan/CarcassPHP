<?php

namespace App\Services;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Repositories\UserRepository;
use Providers\PDOProvider;

class UserService
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    public function create($data): string
    {
        $id = $this->repository->create($data);

        if (is_int($id)) {
            return Response::send([
                'success' => true,
                'result' => [
                    'id' => $id
                ],
            ]);
        } else {
            return Response::sendServerError();
        }
    }

    public function update(int $userId, array $data)
    {
        $user = $this->repository->hasUser($userId);

        if (!$user) {
            return Response::sendNotFoundError();
        }

        if (!Validator::hasAtLeastOneInData($data, [
            'full_name', 'role', 'efficiency'
        ])) {
            return Response::send([
                'success' => true,
                'result' => $user[0]
            ]);
        }

        if ($result = $this->repository->update($data, $userId)) {
            return Response::send([
                'success' => true,
                'result' => $result[0]
            ]);
        }

        return Response::sendServerError();
    }


}
