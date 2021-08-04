<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository {
    /**
     * @return User
     */
    public function byId(?int $id) {
        $user = User::find($id);

        return $user;
    }

    /**
     * @return User[]
     */
    public function all() {
        $users = User::orderBy('created_at')->get();

        return $users;
    }
}