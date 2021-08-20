<?php 
namespace App\Services;

use Exception;
use App\Models\User;
use App\Exceptions\Response;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService {
    /**
     * @var UserRepository
     */
    private $repository;
    
    public function __construct(UserRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            $user = new User;
            $user->fill($data);

            if(env('PW_CRYPT') === true) {
                $user->password = Hash::make($data['password']);
            }

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
                throw new Exception('User not found', 404);
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
                throw new Exception('User not found', 404);
            }

            $token = $user->createToken($tokenName, ['user:actions']);

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
                throw new Exception('User not found', 404);
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
                throw new Exception('User not found', 404);
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