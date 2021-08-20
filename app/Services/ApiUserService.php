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

            if(!$user) {
                throw new Exception('Api User not found', 404);
            }

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

            if(!$user) {
                throw new Exception('Api User not found', 404);
            }

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

            if(!$user) {
                throw new Exception('Api User not found', 404);
            }

            $token = $user->createToken($tokenName, ['basic']);

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
            if(!$requestUser) {
                throw new Exception('Api User not found', 404);
            }

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
            if(!$requestUser) {
                throw new Exception('Api User not found', 404);
            }
            
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