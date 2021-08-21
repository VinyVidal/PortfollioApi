<?php
namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectImage;

class ProjectImageRepository {
    /**
     * @return ProjectImage
     */
    public function byId(?int $id) {
        $image = ProjectImage::find($id);

        return $image;
    }

    /**
     * @return ProjectImage
     */
    public function byPosition(int $position, Project $project) {
        $image = ProjectImage::where('project_id', $project->id)
                        ->where('position', $position)
                        ->first();

        return $image;
    }

    /**
     * @return ProjectImage
     */
    public function first(Project $project) {
        $image = ProjectImage::where('project_id', $project->id)
                        ->orderBy('position', 'asc')
                        ->first();

        return $image;
    }

    /**
     * @return ProjectImage
     */
    public function main(Project $project) {
        $image = ProjectImage::where('project_id', $project->id)
                        ->where('main', true)
                        ->first();
        
        return $image;
    }

    /**
     * @return ProjectImage[]
     */
    public function all() {
        $images = ProjectImage::orderBy('position')
                         ->get();

        return $images;
    }

    /**
     * @return ProjectImage[]
     */
    public function allByProject(Project $project) {
        $images = ProjectImage::where('project_id', $project->id)
                        ->orderBy('position')
                        ->get();

        return $images;
    }

    /**
     * @return ProjectImage[]
     */
    public function allByPositionHigher(int $position, Project $project, $equal = false) {
        $images = ProjectImage::where('project_id', $project->id)
                           ->where('position', $equal ? '>=' : '>', $position)
                           ->orderBy('position')->get();

        return $images;
    }

    /**
     * @return ProjectImage[]
     */
    public function allByPositionLower(int $position, Project $project, $equal = false) {
        $images = ProjectImage::where('project_id', $project->id)
                           ->where('position', $equal ? '<=' : '<', $position)
                           ->orderBy('position')
                           ->get();

        return $images;
    }

    /**
     * @return ProjectImage[]
     */
    public function allByPositionBetween(int $startPosition, int $endPosition, Project $project, $equal = false) {
        $images = ProjectImage::where('project_id', $project->id)
                           ->where('position', $equal ? '>=' : '>', $startPosition)
                           ->where('position', $equal ? '<=' : '<', $endPosition)
                           ->orderBy('position')
                           ->get();

        return $images;
    }

    /**
     * @return int
     */
    public function maxPosition(Project $project) {
        $image = ProjectImage::where('project_id', $project->id)->orderBy('position', 'desc')->first();

        $maxPosition = 0;

        if($image) {
            $maxPosition = $image->position;
        }

        return $maxPosition;
    }

    /**
     * @return int
     */
    public function count(Project $project) {
        $count = ProjectImage::count('project_id', $project->id);

        return $count;
    }
}