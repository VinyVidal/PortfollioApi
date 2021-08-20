<?php
namespace App\Repositories;

use App\Models\Project;
use App\Models\User;

class ProjectRepository {
    /**
     * @return Project
     */
    public function byId(?int $id) {
        $project = Project::find($id);

        return $project;
    }

    /**
     * @return Project
     */
    public function byPosition(int $position) {
        $project = Project::where('position', $position)
                          ->first();

        return $project;
    }

    /**
     * @return Project[]
     */
    public function all() {
        $projects = Project::orderBy('position')
                           ->get();

        return $projects;
    }

    /**
     * @return Project[]
     */
    public function allByUser(User $user) {
        $projects = Project::where('user_id', $user->id)
                           ->orderBy('position')
                           ->get();

        return $projects;
    }

    /**
     * @return Project[]
     */
    public function allByPositionHigher(int $position, User $user, $equal = false) {
        $projects = Project::where('user_id', $user->id)
                           ->where('position', $equal ? '>=' : '>', $position)
                           ->orderBy('position')->get();

        return $projects;
    }

    /**
     * @return Project[]
     */
    public function allByPositionLower(int $position, User $user, $equal = false) {
        $projects = Project::where('user_id', $user->id)
                           ->where('position', $equal ? '<=' : '<', $position)
                           ->orderBy('position')
                           ->get();

        return $projects;
    }

    /**
     * @return Project[]
     */
    public function allByPositionBetween(int $startPosition, int $endPosition, User $user, $equal = false) {
        $projects = Project::where('user_id', $user->id)
                           ->where('position', $equal ? '>=' : '>', $startPosition)
                           ->where('position', $equal ? '<=' : '<', $endPosition)
                           ->orderBy('position')
                           ->get();

        return $projects;
    }

    /**
     * @return int
     */
    public function maxPosition(User $user) {
        $project = Project::where('user_id', $user->id)->orderBy('position', 'desc')->first();

        $maxPosition = 0;

        if($project) {
            $maxPosition = $project->position;
        }

        return $maxPosition;
    }
}