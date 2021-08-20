<?php 
namespace App\Services;

use Exception;
use App\Models\User; #Model
use App\Exceptions\Response;
use App\Repositories\UserRepository; #Repository

class SomethingService {
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
            $model = new User;
            $model->fill($data);
            $model->save();

            return [
                'success' => true,
                'data' => $model
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            $model = $this->repository->byId($id);

            if(!$model) {
                throw new Exception('Resource not found', 404);
            }

            $model->fill($data);
            $model->save();

            return [
                'success' => true,
                'data' => $model
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            $model = $this->repository->byId($id);

            if(!$model) {
                throw new Exception('Resource not found', 404);
            }

            $model->delete();

            return [
                'success' => true,
                'data' => $model
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}