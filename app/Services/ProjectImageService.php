<?php 
namespace App\Services;

use Exception;
use App\Models\ProjectImage;
use App\Exceptions\Response;
use App\Repositories\ProjectImageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectImageService {
    /**
     * @var ProjectImageRepository
     */
    private $repository;
    
    public function __construct(ProjectImageRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store(array $data) {
        try {
            return DB::transaction(function () use ($data) {
                $image = new ProjectImage;
                $image->fill($data);

                if($this->repository->count($image->project) === 0) {
                    $image->main = true;
                    $image->position = 0;
                } else {
                    $image->main = false;

                    if(!$image->position || $image->position > $this->repository->maxPosition($image->project)) {
                        $image->position = $this->repository->maxPosition($image->project) + 1;
                    } else {
                        foreach($this->repository->allByPositionHigher($image->position, $image->project, true) as $img) {
                            $img->position++;
                            $img->save();
                        }
                    }
                }

                $image->save();

                // Store image
                if(isset($data['upload']))
                {
                    $image->path = Storage::url(Storage::disk('public')->putFile('images/project' . $image->project->id, $data['upload']));
                }

                return [
                    'success' => true,
                    'data' => $image
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function update(int $id, array $data) {
        try {
            return DB::transaction(function () use ($id, $data) {
                $image = $this->repository->byId($id);

                if(!$image) {
                    throw new Exception('Project Image not found', 404);
                }
                
                $oldPosition = $image->position;
                $image->fill($data);

                if($image->position < $oldPosition) {
                    foreach($this->repository->allByPositionBetween($image->position, $oldPosition, $image->project, true) as $img) {
                        $img->position++;
                        $img->save();
                    }
                } elseif($image->position > $oldPosition) {
                    foreach($this->repository->allByPositionBetween($oldPosition, $image->position, $image->project, true) as $img) {
                        $img->position--;
                        $img->save();
                    }
                }

                // Update image
                if(isset($data['upload']))
                {
                    Storage::disk('public')->delete( str_replace('/storage/', '', $image->path) );
                    $image->path = Storage::url(Storage::disk('public')->putFile('images/project' . $image->project->id, $data['upload']));
                }

                $image->save();

                return [
                    'success' => true,
                    'data' => $image
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function main(int $id) {
        try {
            return DB::transaction(function () use ($id) {
                $image = $this->repository->byId($id);

                if(!$image) {
                    throw new Exception('Project Image not found', 404);
                }

                $position = $image->position;
                
                $image->main = true;
                $image->position = 0;

                foreach($this->repository->allByPositionHigher($position, $image->project) as $img) {
                    $img->position--;
                    $img->save();
                }

                $image->save();

                return [
                    'success' => true,
                    'data' => $image
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }

    public function delete(int $id) {
        try {
            return DB::transaction(function () use ($id) {
                $image = $this->repository->byId($id);

                if(!$image) {
                    throw new Exception('Project Image not found', 404);
                }

                $position = $image->position;
                $project = $image->project;
                $image->delete();

                foreach($this->repository->allByPositionHigher($position, $project) as $img) {
                    $img->position--;
                    $img->save();
                }

                // Make the first image as the main one
                $first = $this->repository->first($project);
                if($first) {
                    $result = $this->main($first->id);
                    if(!$result['success']) {
                        throw new Exception($result['message'], $result['code']);
                    }
                }

                return [
                    'success' => true,
                    'data' => $image
                ];
            });
        } catch (Exception $ex) {
            return Response::handle($ex);
        }
    }
}