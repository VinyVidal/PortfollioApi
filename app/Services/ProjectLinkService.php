<?php 
namespace App\Services;

use Exception;
use App\Models\ProjectLink;
use App\Exceptions\Response;
use App\Repositories\ProjectLinkRepository;
use Illuminate\Support\Facades\DB;

class ProjectLinkService {
    /**
     * @var ProjectLinkRepository
     */
    private $repository;
    
    public function __construct(ProjectLinkRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            return DB::transaction(function () use ($data) {
                $link = new ProjectLink;
                $link->fill($data);

                $link->main = false;

                if(!$link->position || $link->position > $this->repository->maxPosition($link->project)) {
                    $link->position = $this->repository->maxPosition($link->project) + 1;
                } else {
                    foreach($this->repository->allByPositionHigher($link->position, $link->project, true) as $l) {
                        $l->position++;
                        $l->save();
                    }
                }

                $link->save();

                return [
                    'success' => true,
                    'data' => $link
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            return DB::transaction(function () use ($id, $data) {
                $link = $this->repository->byId($id);

                if(!$link) {
                    throw new Exception('Project Link not found', 404);
                }
                
                $oldPosition = $link->position;
                $link->fill($data);

                if($link->position < $oldPosition) {
                    foreach($this->repository->allByPositionBetween($link->position, $oldPosition, $link->project, true) as $l) {
                        $l->position++;
                        $l->save();
                    }
                } elseif($link->position > $oldPosition) {
                    foreach($this->repository->allByPositionBetween($oldPosition, $link->position, $link->project, true) as $l) {
                        $l->position--;
                        $l->save();
                    }
                }

                $link->save();

                return [
                    'success' => true,
                    'data' => $link
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            $link = $this->repository->byId($id);

            if(!$link) {
                throw new Exception('Project Link not found', 404);
            }

            $position = $link->position;
            $project = $link->project;
            $link->delete();

            foreach($this->repository->allByPositionHigher($position, $project) as $l) {
                $l->position--;
                $l->save();
            }

            return [
                'success' => true,
                'data' => $link
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}