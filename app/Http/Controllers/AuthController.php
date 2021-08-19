<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $repository;
    private $service;

    public function __construct(UserRepository $repository, UserService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }
    public function auth(Request $request) {
        // TODO: create Requests
        $user = $this->repository->byEmail($request->email);
        if($user) {
            $pwMatch = env('PW_CRYPT', true) === true ? Hash::check($request->password, $user->password) : $request->password === $user->password;
            if(!$pwMatch) {
                $user = null;
            }
        }

        if(!$user) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $result = $this->service->createToken($user->id);
        if(!$result['success']) {
            return response()->json([
                'success' => false,
                'status_code' => $result['code'],
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'token' => $result['data']->plainTextToken,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function revoke($id = null) {
        $result = $this->service->revokeToken(request()->user(), $id);

        if(!$result['success']) {
            return response()->json([
                'success' => false,
                'status_code' => $result['code'],
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
        ], 200);
    }

    public function revokeAll() {
        $result = $this->service->revokeAllTokens(request()->user());

        if(!$result['success']) {
            return response()->json([
                'success' => false,
                'status_code' => $result['code'],
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
        ], 200);
    }
}
