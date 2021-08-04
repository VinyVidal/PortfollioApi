<?php
namespace App\Repositories;

use App\Models\User;

class SomethingRepository {
    /**
     * @return User
     */
    public function byId(?int $id) {
        $model = User::find($id);

        return $model;
    }

    /**
     * @return User[]
     */
    public function all() {
        $models = User::orderBy('created_at')->get();

        return $models;
    }
}