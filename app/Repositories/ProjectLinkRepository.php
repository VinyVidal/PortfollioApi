<?php
namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectLink;

class ProjectLinkRepository {
    /**
     * @return ProjectLink
     */
    public function byId(?int $id) {
        $link = ProjectLink::find($id);

        return $link;
    }

    /**
     * @return ProjectLink
     */
    public function byPosition(int $position, Project $project) {
        $link = ProjectLink::where('project_id', $project->id)
                        ->where('position', $position)
                        ->first();

        return $link;
    }

    /**
     * @return ProjectLink[]
     */
    public function all() {
        $links = ProjectLink::orderBy('position')
                         ->get();

        return $links;
    }

    /**
     * @return ProjectLink[]
     */
    public function allByProject(Project $project) {
        $links = ProjectLink::where('project_id', $project->id)
                        ->orderBy('position')
                        ->get();

        return $links;
    }

    /**
     * @return ProjectLink[]
     */
    public function allByPositionHigher(int $position, Project $project, $equal = false) {
        $links = ProjectLink::where('project_id', $project->id)
                           ->where('position', $equal ? '>=' : '>', $position)
                           ->orderBy('position')->get();

        return $links;
    }

    /**
     * @return ProjectLink[]
     */
    public function allByPositionLower(int $position, Project $project, $equal = false) {
        $links = ProjectLink::where('project_id', $project->id)
                           ->where('position', $equal ? '<=' : '<', $position)
                           ->orderBy('position')
                           ->get();

        return $links;
    }

    /**
     * @return ProjectLink[]
     */
    public function allByPositionBetween(int $startPosition, int $endPosition, Project $project, $equal = false) {
        $links = ProjectLink::where('project_id', $project->id)
                           ->where('position', $equal ? '>=' : '>', $startPosition)
                           ->where('position', $equal ? '<=' : '<', $endPosition)
                           ->orderBy('position')
                           ->get();

        return $links;
    }

    /**
     * @return int
     */
    public function maxPosition(Project $project) {
        $link = ProjectLink::where('project_id', $project->id)->orderBy('position', 'desc')->first();

        $maxPosition = 0;

        if($link) {
            $maxPosition = $link->position;
        }

        return $maxPosition;
    }
}