<?php
namespace App\Repositories;

use App\Models\ApiUser;

class ApiUserRepository {
    /**
     * @return ApiUser
     */
    public function byId(?int $id) {
        $apiUser = ApiUser::find($id);

        return $apiUser;
    }

    /**
     * @return ApiUser
     */
    public function byEmail($email) {
        $apiUser = ApiUser::where('email', $email)->first();

        return $apiUser;
    }

    /**
     * @return ApiUser[]
     */
    public function all() {
        $users = ApiUser::orderBy('created_at')->get();

        return $users;
    }
}