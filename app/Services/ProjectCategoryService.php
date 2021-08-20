<?php 
namespace App\Services;

use Exception;
use App\Models\ProjectCategory;
use App\Exceptions\Response;
use App\Repositories\ProjectCategoryRepository;
use Illuminate\Support\Facades\DB;

class SomethingService {
    /**
     * @var ProjectCategoryRepository
     */
    private $repository;
    
    public function __construct(ProjectCategoryRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            return DB::transaction(function () use ($data) {
                $category = new ProjectCategory;
                $category->fill($data);

                if(!$category->position || $category->position >= $this->repository->maxPosition($category->user)) {
                    $category->position = $this->repository->maxPosition($category->user) + 1;
                } else {
                    foreach($this->repository->allByPositionHigher($category->position, $category->user, true) as $p) {
                        $p->position++;
                        $p->save();
                    }
                }

                $category->save();

                return [
                    'success' => true,
                    'data' => $category
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            return DB::transaction(function () use ($id, $data) {
                $category = $this->repository->byId($id);
                $oldPosition = $category->position;
                $category->fill($data);

                if($category->position < $oldPosition) {
                    foreach($this->repository->allByPositionBetween($category->position, $oldPosition, $category->user, true) as $p) {
                        $p->position++;
                        $p->save();
                    }
                } elseif($category->position > $oldPosition) {
                    foreach($this->repository->allByPositionBetween($oldPosition, $category->position, $category->user, true) as $p) {
                        $p->position--;
                        $p->save();
                    }
                }

                $category->save();

                return [
                    'success' => true,
                    'data' => $category
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            $category = $this->repository->byId($id);

            if(!$category) {
                throw new Exception('Project Category not found', 404);
            }

            $position = $category->position;
            $user = $category->user;
            $category->delete();

            foreach($this->repository->allByPositionHigher($position, $user) as $p) {
                $p->position--;
                $p->save();
            }

            return [
                'success' => true,
                'data' => $category
            ];
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}