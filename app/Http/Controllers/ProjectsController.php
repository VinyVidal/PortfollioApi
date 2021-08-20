<?php

namespace App\Http\Controllers;

use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    private $service;
    private $repository;

    public function __construct(ProjectService $service, ProjectRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'data' => $this->repository->all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->service->store($request->all());
        
        if(!$result['success']) {
            return response()->json([
                'success' => true,
                'status_code' => $result['code'],
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'status_code' => 201,
            'data' => $result['data']
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'data' => $this->repository->byId($id)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = $this->service->update($id, $request->all());

        if(!$result['success']) {
            return response()->json([
                'success' => true,
                'status_code' => $result['code'],
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'data' => $result['data']
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->delete($id);

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
            'data' => $result['data']
        ], 200);
    }
}
