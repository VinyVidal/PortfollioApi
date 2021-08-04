<?php 
namespace App\Services;

use Exception;
use App\Models\User; #Model
use App\Exceptions\Response;

class SomethingService {
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
            $model = User::find($id);
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
            $model = User::find($id);
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