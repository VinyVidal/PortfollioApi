<?php 
namespace App\Services;

use Exception;
use App\Models\User;
use App\Exceptions\Response;
use App\Repositories\ApiUserRepository;
use Laravel\Sanctum\NewAccessToken;

class ApiUserService {
    /**
     * @var ApiUserRepository
     */
    private $repository;
    
    public function __construct(ApiUserRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            $user = new User;
            $user->fill($data);
            $user->save();

            return [
                'success' => true,
                'data' => $user
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            $user = $this->repository->byId($id);
            $user->fill($data);
            $user->save();

            return [
                'success' => true,
                'data' => $user
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            $user = $this->repository->byId($id);
            $user->delete();

            return [
                'success' => true,
                'data' => $user
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function createToken(int $userId, $tokenName = 'access-token') {
        try {
            $user = $this->repository->byId($userId);
            // TODO: Issue token abilities
            $token = $user->createToken($tokenName);

            return [
                'success' => true,
                'data' => $token
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function revokeToken($requestUser, ?int $tokenId = null) {
        try {
            if($tokenId) {
                $requestUser->tokens()->where('id', $tokenId)->delete();
            } else {
                $requestUser->currentAccessToken()->delete();
            }

            return [
                'success' => true,
                'data' => $requestUser
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function revokeAllTokens($requestUser) {
        try {
            $requestUser->tokens()->delete();

            return [
                'success' => true,
                'data' => $requestUser
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}