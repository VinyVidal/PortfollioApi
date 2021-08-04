<?php 
namespace App\Services;

use Exception;
use App\Models\Project;
use App\Exceptions\Response;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Facades\DB;

class SomethingService {
    /**
     * @var ProjectRepository
     */
    private $repository;
    
    public function __construct(ProjectRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            DB::transaction(function () use ($data) {
                $project = new Project;
                $project->fill($data);

                if(!$project->position || $project->position >= $this->repository->maxPosition($project->user)) {
                    $project->position = $this->repository->maxPosition($project->user) + 1;
                } else {
                    foreach($this->repository->allByPositionHigher($project->position, $project->user, true) as $p) {
                        $p->position++;
                        $p->save();
                    }
                }

                $project->save();

                return [
                    'success' => true,
                    'data' => $project
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            DB::transaction(function () use ($id, $data) {
                $project = $this->repository->byId($id);
                $oldPosition = $project->position;
                $project->fill($data);

                if($project->position < $oldPosition) {
                    foreach($this->repository->allByPositionBetween($project->position, $oldPosition, $project->user, true) as $p) {
                        $p->position++;
                        $p->save();
                    }
                } elseif($project->position > $oldPosition) {
                    foreach($this->repository->allByPositionBetween($oldPosition, $project->position, $project->user, true) as $p) {
                        $p->position--;
                        $p->save();
                    }
                }

                $project->save();

                return [
                    'success' => true,
                    'data' => $project
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            $project = $this->repository->byId($id);
            $position = $project->position;
            $project->delete();

            foreach($this->repository->allByPositionHigher($position, $project->user) as $p) {
                $p->position--;
                $p->save();
            }

            return [
                'success' => true,
                'data' => $project
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}